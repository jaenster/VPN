<?php

namespace VPN\Deamon\Router;


use Rawsocket\Model\Protocol\IPv4;

class IPv4Router
{
    protected $router;
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function parseIPPacket(IPv4 $ipPacket){
        $ipDst = $ipPacket->getDstIP();
        $ipSrc = $ipPacket->getSrcIP();

        print $ipSrc->getNormal() .'->'.$ipDst->getNormal().PHP_EOL;

        try {
            $route = $this->router->getRoutes()->getRoute($ipDst);
        } catch (\Exception $e) {
            // no such route
            return;
        }
        var_dump($route);

    }
}