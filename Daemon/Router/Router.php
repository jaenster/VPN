<?php

namespace VPN\Deamon\Router;

use Kernel\Kernel;
use Kernel\Runnable;
use VPN\Daemon\Router\NetworkDevice;
use Rawsocket\Model\Protocol\IPv4;
use Rawsocket\Route\Routes;
use Rawsocket\Model\Protocol\Ethernet;

class Router implements Runnable
{
    protected $networkInterface,
        $IPv4Router,
        $routes;


    public function __construct(NetworkDevice $networkInterface)
    {
        $this->networkInterface = $networkInterface;
        $this->IPv4Router = (new IPv4Router($this));
        $this->routes = (new Routes());
        Kernel::register($this);

    }
    public function start() : void
    {

    }
    public function run() : void
    {

    }

    public function parseEthernetPacket(Ethernet $packet): void
    {
        print 'HERE'.PHP_EOL;
        try {
            $ip = $packet->getNextLayer();
        } catch (\Throwable $e){
            // Not a ipv4 packet, since there is no next layer
            return;
        }

        if ($ip instanceof IPv4){
            // Its a IPv4 packet. We may need to route this
            $this->IPv4Router->parseIPPacket($ip);
        }
    }
    public function getRoutes() : Routes
    {
        return $this->routes;
    }
}