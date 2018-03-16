<?php

namespace Configuration;

use Rawsocket\Layer\IPv4;
use Rawsocket\Model\Route;
use Rawsocket\Route\Routes;
use VPN\Transfer\Encapsulation\SimpleEncapsulation;
use VPN\Transfer\Encryption\NoEncryption;
use VPN\Transfer\Protocol\Protocolv0_1;

class ServerConfig
{
    public $routes, $name, $port, $hostname,$ip, $pass, $client, $encapsulation, $encryption, $socket,$server,$protocol;

    public function __construct()
    {
        $this->routes = new Routes();
        $this->encapsulation = null;
        $this->encryption = null;
        $this->protocol = new Protocolv0_1($this);
    }

    public function addRoute(string $route): void
    {
        // get the network (without the /)
        $network = substr($route, 0, strpos($route, '/'));

        // convert the /x to a subnet mask
        $subnet = IPv4::cidr2mask((int)substr($route, strpos($route, '/') + 1));

        // Add the route
        $this->routes->addRoute(new Route($network, '', $subnet));
    }

    public function setEncapsulation(string $name): void
    {
        switch (strtolower($name)) {
            case 'simple':
            default:
                $this->encapsulation = new SimpleEncapsulation();
                break;
        }
    }

    public function setEncryption(string $name): void
    {
        switch (strtolower($name)) {
            case 'noencryption':
            default:
                $this->encryption = new NoEncryption($this);
                break;
        }
    }


    // Called by config
    public function done(): void
    {
        // If no Encapsulation chosen, choose the default
        if ($this->encapsulation === null) {
            print 'here -- encapsulation '.PHP_EOL;
            $this->setEncapsulation('');
        }

        // If no encryption chosen, choose the default
        if ($this->encryption === null) {
            print 'here -- encryption '.PHP_EOL;
            $this->setEncryption('');
        }

        $this->ip = gethostbyname($this->hostname);
        // Only needed a to start with the kernel,

    }


    public static function getByName(string $name): ServerConfig
    {
        $servers = Conf::$serverConfigs;
        if (!isset($servers[$name])) {
            throw new \Exception('no such server');
        }
        return $servers[$name];
    }

    public static function getByIP($ip): ServerConfig
    {
        $servers = Conf::$serverConfigs;
        foreach ($servers as $server){
            if (!$server instanceof ServerConfig) { continue; }
            // The ip we are looking for?
            if ($ip == gethostbyname($server->hostname)) {

                return $server;
            }
        }
        throw new \Exception('no such server');
    }
}