<?php

namespace Rawsocket\Route;


use Rawsocket\Exceptions\NoSuchRoute;
use Rawsocket\Model\IPv4Address;
use Rawsocket\Layer\IPv4 as IPv4Layer;
use Rawsocket\Model\Route;
class Routes
{
    private $routes = [];

    // Function that adds the default system routes
    public function addSystemRoutes() : self{
        // Get core routes.
        $data = shell_exec('route');

        // split on lines
        $data = explode(PHP_EOL,$data);

        // Remove headers
        unset($data[1],$data[0]);

        // Last routes might effect a smaller early, yet have more impact.
        // So reverse the array
        $data = array_reverse($data);


        // Loop trough each line
        foreach ($data as $line)
        {
            // Remove the unix "default" to 0.0.0.0
            $line = str_replace('default','0.0.0.0',$line);

            // Match all words in the line
            preg_match_all("/[\w\A\.\:^\*]+/", $line,$output);

            // A valid route should have 8 elements // Destination, Gateway, Genmask, Flags, Ref, Use, Iface
            if(count($output[0]) === 8){
                $this->addRoute(new Route($output[0][0],$output[0][1],$output[0][2],$output[0][7]));
            }

        }
        return $this;
    }
    public function getRoutes() : array
    {
        return $this->routes;
    }

    // Add a route
    public function addRoute(Route $route) : self
    {
        $this->routes[] = $route;
        return $this;
    }

    // Get the next Hop
    public function getRoute(IPv4Address $ip) : Route
    {
        if (is_array($this->routes)) {
            foreach ($this->routes as $route) {
                if (!$route instanceof Route) {
                    continue;
                }
                if (IPv4Layer::ipInRange($ip, $route->destination, $route->subnet)) {
                    // Should route $ip via $route->gateway on interface $route->interface
                    return $route;
                };
            }
        }
        throw new NoSuchRoute($ip->getNormal());
    }
    public function resetRoutes() : void
    {
        $this->routes = [];
    }


}