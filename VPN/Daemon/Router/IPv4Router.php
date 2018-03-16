<?php

namespace VPN\Deamon\Router;


use Configuration\Conf;
use Rawsocket\Model\Packet;
use Rawsocket\Model\Protocol\IPv4;
use VPN\Transfer\Protocol\Protocol;

class IPv4Router
{
    protected $router;
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function parseIPPacket(IPv4 $ipPacket,Packet $packet)
    {
        // get Dst ip
        $ipDst = $ipPacket->getDstIP();

        // Ingore packets that are directed to us
        if ($ipDst->getNormal() == $this->router->networkInterface->ip){
            return ;
        }

        print $ipDst->getNormal().PHP_EOL;
        try {
            $serverConfig = $this->router->getRoutes($ipDst);
        } catch (\Exception $e) {
            print 'No such route'.PHP_EOL;
            // no such route
            return;
        }

        // send the packet to the server
        $serverConfig->protocol->handleSendPacket($ipPacket->getRaw(),Protocol::TYPE_IPv4);
    }
}