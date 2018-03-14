<?php

namespace Configuration;

use Kernel\Kernel;
use Kernel\Runnable;
use Rawsocket\Layer\IPv4;
use Rawsocket\Model\Route;
use Rawsocket\Route\Routes;
use Socket\Client;
use VPN\Transfer\Encapsulation\Simple;
use VPN\Transfer\Encryption\NoEncryption;

class ServerConfig implements Runnable
{
    public $routes,$name,$port,$hostname,$pass,$client,$encapsulation,$encryption,$socket;

    public function __construct()
    {
        $this->routes = new Routes();
        $this->encapsulation = null;
        Kernel::register($this);
    }

    public function addRoute(string $route) : void
    {
        // get the network (without the /)
        $network = substr($route, 0, strpos($route, '/'));

        // convert the /x to a subnet mask
        $subnet = IPv4::cidr2mask((int)substr($route, strpos($route, '/') + 1));

        // Add the route
        $this->routes->addRoute(new Route($network, '', $subnet));
    }
    public function setEncapsulation(string $name) : void
    {
        switch (strtolower($name))
        {
            case 'simple':
            default:
                $this->encapsulation = new Simple();
                break;
        }
    }
    public function setEncryption(string $name) : void
    {
        switch (strtolower($name))
        {
            case 'noencryption':
            default:
                $this->encryption = new NoEncryption();
                break;
        }
    }
    public function start() : void
    {
        // If no Encapsulation chosen, choose the default
        if ($this->encapsulation === null)
        {
            $this->setEncapsulation('');
        }

        // If no encryption chosen, choose the default
        if ($this->encryption === null)
        {
            $this->setEncryption('');
        }

        if (!$this->name === null) {
            new Client($this->hostname,$this->port,\VPN\Transfer\Client::class,SOL_TCP);
        }

        // Only needed a to start with the kernel,
        Kernel::detach($this);

    }
    public function run() : void
    {

    }
    public function setClient(\VPN\Transfer\Client $client) : void
    {
        $this->client = $client;
    }
    public function getClient() : \VPN\Transfer\Client
    {
        return $this->client;
    }
}