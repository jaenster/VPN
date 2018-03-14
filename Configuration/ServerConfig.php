<?php

namespace Configuration;

use Rawsocket\Layer\IPv4;
use Rawsocket\Model\Route;
use Rawsocket\Route\Routes;
class ServerConfig
{
    public $routes,$name,$port,$pass;

    public function __construct()
    {
        $this->routes = new Routes();
    }

    public function addRoute(string $route)
    {
        // get the network (without the /)
        $network = substr($route,0,strpos($route,'/'));

        // convert the /x to a subnet mask
        $subnet = IPv4::cidr2mask((int) substr($route,strpos($route,'/')+1));

        // Add the route
        $this->routes->addRoute(new Route($network,'',$subnet));
    }


}