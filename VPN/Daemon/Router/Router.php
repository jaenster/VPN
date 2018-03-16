<?php

namespace VPN\Deamon\Router;

use Kernel\Kernel;
use Kernel\Runnable;
use Rawsocket\Model\IPv4Address;
use Configuration\ServerConfig;
use Rawsocket\Model\Packet;
use VPN\Daemon\Router\NetworkDevice;
use Rawsocket\Model\Protocol\IPv4;
use Rawsocket\Model\Protocol\Ethernet;
use Configuration\Conf;

class Router implements Runnable
{
    public $networkInterface;
    protected    $IPv4Router,
        $routes;


    public function __construct(NetworkDevice $networkInterface)
    {
        $this->networkInterface = $networkInterface;
        $this->IPv4Router = (new IPv4Router($this));
        Kernel::register($this);

    }
    public function start() : void
    {

    }
    public function run() : void
    {

    }

    public function parseEthernetPacket(Ethernet $ethernet,Packet $packet): void
    {
        try {
            $ip = $ethernet->getNextLayer();
        } catch (\Throwable $e){
            // Not a ipv4 packet, since there is no next layer
            return;
        }

        if ($ip instanceof IPv4){
            // Its a IPv4 packet. We may need to route this
            $this->IPv4Router->parseIPPacket($ip,$packet);
        }
    }
    public function getRoutes(IPv4Address $ip) : ServerConfig
    {
        $servers = Conf::getServerConfigs();
        foreach ($servers as $server){
            try{

                // Get server config
                $server = ServerConfig::getByName($server);

                // See if there is a route, if not it throws an error
                $server->routes->getRoute($ip);

                // Return server, if we come here
                return $server;
            }  catch (\Exception $e) {
                // Pass error
                continue; // This isn't the server we are looking for
            }
        }
        throw new \Exception('No route found');

    }
}