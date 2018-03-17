<?php

namespace VPN\Configuration;

use Rawsocket\Layer\IPv4;
use Rawsocket\Model\IPv4Address;
use VPN\Daemon\Router\NetworkDevice;
use VPN\Transfer\Transport;

class Conf
{
    public static $conf,$serverConfigs,$transport;
    public static function init(string $configfile)
    {
        foreach (explode("\n", file_get_contents($configfile)) as $line) {
            // Remove additional enters;
            $line = str_replace("\r",'',$line);

            // Remove comments ( // and/or # )
            $line = preg_replace("/((^|[\s]*)([\#]|\/\/).*$)/", "", $line);

            // Error handling
            if (strlen($line) === 0) {
                continue;
            }

            $var = explode('=',$line);
            // error handling
            if (!count($var) == 2){
                continue;
            }
            // Set local vars, to be more clear
            $key = $var[0];
            $value =$var[1];

            switch(true)
            {
                case self::startsWith('server.',$key):
                    // parse server name
                    $server = substr($key,7);
                    $server = substr($server,0,strpos($server,'.'));

                    // convert key to remove leading 'server.Name'
                    $key = substr($key,strpos(substr($key,8),'.')+9);

                    self::parseServerConf($server,$key,$value);
                    break;

                case self::startsWith('interface',$key):
                    (new NetworkDevice($value));
                    self::$conf[strtolower($key)] = $value;
                    break;

                case self::startsWith('gateway',$key):
                    self::$conf[strtolower($key)] = (new IPv4Address())->setIpFromString($value);
                    break;

                default:
                    self::$conf[strtolower($key)] = $value;
            }
        }

        foreach (self::$serverConfigs as $serverConfig)
        {
            if (!$serverConfig instanceof ServerConfig) { continue; }
            $serverConfig->done();
        }
    }

    public static function getEnv($name){
        if (!isset(self::$conf[strtolower($name)])){
            throw new \Exception('Env '.$name.' isn\'t set');
        }
        return self::$conf[strtolower($name)];
    }
    private static function startsWith(string $needle,string $haystack) : bool
    {
        return strtolower(substr( $haystack,0, strlen($needle) )) === $needle;
    }
    private static function parseServerConf(string $server,string $key, string $value)
    {
        if (isset(self::$serverConfigs[$server])) {
            $obj = self::$serverConfigs[$server];
        } else {
            $obj = new ServerConfig();
        }
        if (!$obj instanceof ServerConfig) {
            return;
        }
        switch (true){
            case self::startsWith('route.',$key):
                $obj->addRoute($value); // The x.x.x.x/mask
                break;

            case self::startsWith('encapsulation',$key):
                $obj->setEncapsulation($value);
                break;

            case self::startsWith('encryption',$key):
                $obj->setEncryption($value);
                break;

            default:
                $obj->$key = $value;
                break;
        }
        self::$serverConfigs[$server] = $obj;
    }
    public static function getServerConfigs() : array
    {
        return array_keys(self::$serverConfigs);
    }
    public static function getTransport() : Transport
    {
        return self::$transport;
    }
    public static function getGateway() : IPv4Address
    {
        return self::$conf['gateway'];
    }
}


