<?php

namespace Rawsocket\Layer;

use Rawsocket\Exceptions\InvalidInterface;
use Rawsocket\Exceptions\InvalidMacAddress;
use Rawsocket\Model\IPv4Address;
use Rawsocket\Model\MacAddress;


class Ethernet
{
    private static $arptable = [];
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

    static function getMacOfIP(IPv4Address $ip) : MacAddress
    {
        // ToDo: retrieve with SimplePcap
        // This is the dirty and slow way.


        // We have it saved in our ARP Table?
        foreach (self::$arptable as $item){
            if ($item['ip'] == $ip->getNormal())
            {
                return $item['mac'];
            }
        }

        // send machine a ping
        shell_exec('ping '.$ip->getNormal().' -c 1 ');


        // get the arp table for
        $output = shell_exec('arp '.$ip->getNormal());

        // Do we have a arp entry?
        if (strpos($output,'no entry')
            || !preg_match("/([0-9a-f]{2}:[0-9a-f]{2}:[0-9a-f]{2}:[0-9a-f]{2}:[0-9a-f]{2}:[0-9a-f]{2})\s*\S*\s*(\w*)/", $output, $arp))
        {
            throw new InvalidMacAddress();
        }

        // create the mac address
        $mac = (new MacAddress())->setAsHex($arp[1]);

        // Add to the arp table
        self::$arptable[] =  [  'mac'=>$mac,
                                'device'=>$arp[2],
                                'ip'=>$ip->getNormal()
                            ];
        return $mac;
    }
    static function getDevOfIP(IPv4Address $ip)
    {
        var_dump(self::$arptable);
        // We have it saved in our ARP Table?
        foreach (self::$arptable as $item){
            if ($item['ip'] == $ip->getNormal())
            {
                return $item['device'];
            }
        }
    }

}