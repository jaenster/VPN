<?php

namespace VPN\Deamon\Router;


use Rawsocket\Pcap\DumpablePacket;
use VPN\Configuration\Conf;
use Rawsocket\Model\Packet;
use Rawsocket\Model\Protocol\IPv4;
use VPN\Transfer\Protocol\BaseProtocol;

class IPv4Router
{
    protected $router;
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function parseIPPacket(IPv4 $ipPacket)
    {
        // get Dst ip
        $ipDst = $ipPacket->getDstIP();

        // Ingore packets that are directed to us
        if ($ipDst->getNormal() == $this->router->networkInterface->ip){
            return ;
        }
        try {
            $serverConfig = $this->router->getRoutes($ipDst);
        } catch (\Exception $e) {
            // no such route
            return;
        }
        //print new DumpablePacket($ipPacket->getRaw());
        // send the packet to the server
        $serverConfig->protocol->send(BaseProtocol::TYPE_IPv4,$ipPacket->getRaw());
    }
}