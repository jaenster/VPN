<?php

namespace Rawsocket\Layer;

use Rawsocket\Exceptions\InvalidInterface;
use Rawsocket\Model\MacAddress;


class Ethernet
{

    static function getMacOfDevice($interface) : MacAddress{
        // Get path
        $path = '/sys/class/net/'.$interface.'/address';

        // Error handling. Interface needs to exist
        if (!file_exists($path)){
            throw new InvalidInterface($interface);
        }

        $mac =  file_get_contents($path);
        $mac = substr($mac,0,-1);

        // Found it, return MacAddress
        return (new MacAddress())->setAsHex($mac);

    }

}