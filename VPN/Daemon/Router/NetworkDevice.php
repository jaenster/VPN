<?php

namespace VPN\Daemon\Router;

use Rawsocket\Exceptions\NotALayerInterface;
use Rawsocket\Model\Protocol\IP;
use Rawsocket\Pcap\DumpablePacket;
use VPN\Deamon\Router\Router;
use VPN\Kernel\Kernel;
use VPN\Kernel\Runnable;

use Rawsocket\Builder\NetworkInterface;
use Rawsocket\Layer\Ethernet;
use Rawsocket\Layer\IPv4;
use Rawsocket\Model\IPv4Address;
use Rawsocket\Model\MacAddress;
use Rawsocket\Model\Packet;
use Rawsocket\Pcap\SimplePcap;


class NetworkDevice implements NetworkInterface,Runnable
{
    private static $networkInterfaces = [];
    public $macAddress, // MacAddress
        $ip,            // IPAddress
        $device, // The interface where it sends it packets over
        $simplePcap,
        $router,
        $time;
    public function __construct(string $device)
    {

        $this->macAddress = (new MacAddress())->setAsHex(Ethernet::getMacOfDevice($device));
        $this->ip = IPv4::getIPFromInterface($device);
        $this->device = $device;

        Kernel::register($this);

        // Create a new router;
        $this->router = (new Router($this));

        self::$networkInterfaces[] = $this;
        $this->simplePcap =  new SimplePcap(
            $this->device,
            '',
            4096,
            0);
    }

    public function start() : void
    {
        // Start the network interface

    }

    public function run() : void
    {
        // PHPStorm check.
        if (!$this->simplePcap instanceof SimplePcap ) { return; }
        if (!$this->macAddress instanceof MacAddress){ return ; }

        // the simplePcap magic
        $this->time = [];
        $this->time[0] = mTime();
        $pcapPacket = $this->simplePcap->get();
        if ($pcapPacket === NULL){
            return; // No packet's
        }
        $this->time['pcap'] = mTime()-$this->time[0];
        $this->time[0] = mTime(); // reset time

        $packet = new Packet($pcapPacket);
        $ethernet = $packet->getEthernet();
        $this->time['Ethernet'] = mTime()-$this->time[0];
        $this->time[0] = mTime(); // reset time

        // not IP traffic? skip
        try {
            if (!$ethernet->getNextLayer() instanceof IP)
            {
                // not IP traffic
                return ;
            }
        } catch (NotALayerInterface $e)
        {
            // not IP traffic
            return ;
        }


        // Is it a broadcast, or a directed straight at us?
        if ($ethernet->getMacDst()->getRaw() === $this->macAddress->getRaw()){
            //|| $ethernet->getMacDst()->getNormal() === 'ff:ff:ff:ff:ff:ff'){

            // Let the kernel parse this packet
            Kernel::callMethod('parseEthernetPacket',[$ethernet]);
            $this->time['Kernel'] = mTime()-$this->time[0];

        }

    }


    public function getIPv4(): IPv4Address
    {
        return $this->ip;
    }
    public function getDeviceName(): string
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
    public static function getNetworkInterfaceByDeviceName($name) : self
    {
        foreach (self::$networkInterfaces as $ni)
        {
            // Error handling
            if (!$ni instanceof self) {continue;}
            if ($ni->getDeviceName() == $name)
            {
                return $ni;
            }
        }
        //throw new \Exception('No Such Device');
        return new self($name);
    }
}