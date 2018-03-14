<?php

namespace VPN\Daemon\Router;

use Kernel\Kernel;
use Kernel\Runnable;
use Rawsocket\Builder\NetworkInterface;
use Rawsocket\Layer\Ethernet;
use Rawsocket\Layer\IPv4;
use Rawsocket\Model\IPv4Address;
use Rawsocket\Model\MacAddress;
use Rawsocket\Model\Packet;
use Rawsocket\Pcap\SimplePcap;
use VPN\Deamon\Router\Router;

class NetworkDevice implements NetworkInterface,Runnable
{
    public $macAddress, // MacAddress
        $ip,            // IPAddress
        $device, // The interface where it sends it packets over
        $simplePcap,
        $router;
    public function __construct(string $device)
    {

        $this->macAddress = (new MacAddress())->setAsHex(Ethernet::getMacOfDevice($device));
        $this->ip = IPv4::getIPFromInterface($device);
        $this->device = $device;

        Kernel::register($this);

        // Create a new router;
        $this->router = (new Router($this));
    }

    public function start() : void
    {

        // Start the network interface
        $this->simplePcap =  new SimplePcap(
            $this->device,
            '',
            4096,
            1);




    }

    public function run() : void
    {
        // PHPStorm check.
        if (!$this->simplePcap instanceof SimplePcap ) { return; }
        if (!$this->macAddress instanceof MacAddress){ return ; }
        // Get a packet
        $pcapPacket = $this->simplePcap->get();
        if ($pcapPacket === NULL){
            return;
        }
        $ethernet = (new Packet($pcapPacket))->getEthernet();


        // Is it a broadcast, or a directed straight at us?
        if ($ethernet->getMacDst()->getRaw() === $this->macAddress->getRaw()
            || $ethernet->getMacDst()->getNormal() === 'ff:ff:ff:ff:ff:ff'){

            // Let the kernel parse this packet
            Kernel::callMethod('parseEthernetPacket',$ethernet);
        }

    }


    public function getIPv4(): IPv4Address
    {
        return $this->ip;
    }
    public function getInterface(): string
    {
        return $this->device;
    }
    public function getMac(): MacAddress
    {
        return $this->macAddress;
    }
    public function getSimplePcap() : SimplePcap
    {
        return $this->simplePcap;
    }
    public function getRouter() : Router
    {
        return $this->router;
    }

}