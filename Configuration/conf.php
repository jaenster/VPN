<?php

namespace Configuration;

class conf
{
    public static $conf,$servers;
    public static function init()
    {
        foreach (explode("\n", file_get_contents('../.env')) as $line) {
            // Remove additional enters;
            $line = str_replace("\r",'',$line);

            // Remove comments ( // and/or # )
            $line = preg_replace("/((^|[\s]*)([\#]|\/\/).*$)/", "", $line);

            // Error handling
            if (strlen($line) === 0) {
                continue;
            }

            $var = explode('=',$line);
            // error handeling
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
                default:
                    self::$conf[$key] = $value;
            }

        }
    }

    public static function getEnv($name){
        if (!isset(self::$conf[$name])){
            throw new \Exception('Env '.$name.' isn\'t set');
        }
        return self::$conf[$name];
    }

    public static function getServer($name) : array
    {

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
            var_dump($obj);
            return;
        }
        switch (true){
            case self::startsWith('route.',$key):
                $obj->addRoute($value); // The x.x.x.x/mask
                break;
            default:
                $obj->$key = $value;
                break;
        }
        self::$servers[$server] = $obj;
    }
}


// Load the config
conf::init();