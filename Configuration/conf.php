<?php

namespace Configuration;

use VPN\Daemon\Router\NetworkDevice;
use VPN\Transfer\Encapsulation\Simple;
use VPN\Transfer\Encryption\NoEncryption;

class Conf
{
    private static $conf,$servers;
    public static function init()
    {
        foreach (explode("\n", file_get_contents('.env')) as $line) {
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
                    break;
                default:
                    self::$conf[strtolower($key)] = $value;
            }
        }

        // Config loaded.
        // Encryption
        switch (!isset(self::$conf['encryption']) || strtolower(self::$conf['encryption']))
        {
            case 'noencryption':
            default:
                self::$conf['encryption'] = new NoEncryption();
        }

        // Encapsulation
        switch (!isset(self::$conf['encapsulation']) || strtolower(self::$conf['encapsulation']))
        {
            case 'simple':
            default:
                self::$conf['encapsulation'] = new Simple();
        }
    }

    public static function getEnv($name){
        if (!isset(self::$conf[strtolower($name)])){
            throw new \Exception('Env '.$name.' isn\'t set');
        }
        return self::$conf[strtolower($name)];
    }

    public static function getServer(string $name) : ServerConfig
    {
        if (!isset(self::$servers[$name])){
            throw new \Exception('no such server');
        }
        return self::$servers[$name];
    }
    private static function startsWith(string $needle,string $haystack) : bool
    {
        return strtolower(substr( $haystack,0, strlen($needle) )) === $needle;
    }
    private static function parseServerConf(string $server,string $key, string $value)
    {
        if (isset(self::$servers[$server])) {
            $obj = self::$servers[$server];
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
            default:
                $obj->$key = $value;
                break;
        }
        self::$servers[$server] = $obj;
    }
    public static function getServers() : array
    {
        return array_keys(self::$servers);
    }
}


// Load the config
Conf::init();