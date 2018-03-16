<?php

namespace VPN\Transfer\Protocol;


use Rawsocket\Builder\IPv4Builder;
use Rawsocket\Exceptions\InvalidMacAddress;
use Rawsocket\Model\Protocol\IPv4;
use Rawsocket\Layer\Ethernet;
use VPN\Daemon\Router\NetworkDevice;

trait ParseIPv4
{
    protected function parseIPv4(string $data)
    {
        $IPv4 = new IPv4($data);
        $ipDst = $IPv4->getDstIP();
        print $IPv4->getSrcIP()->getNormal().' -> '.$ipDst->getNormal().PHP_EOL;

        try {
            $macAddress =  Ethernet::getMacOfIP($ipDst);
            $device = Ethernet::getDevOfIP($ipDst);
        } catch (InvalidMacAddress $mac) {
            // No mac address found, we cant route it
            return;
        }

        //$etherner = new EthernetBuilder(NetworkDevice::getNetworkInterfaceByDeviceName($device),$macAddress,EtherType::get(0x0800));

        $builder = new IPv4Builder(NetworkDevice::getNetworkInterfaceByDeviceName($device),$macAddress,$data);
        $builder->build();
        $builder->send();

    }
}