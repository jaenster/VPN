<?php


namespace Rawsocket\Model;


class Route
{
    public $destination, $gateway, $subnet, $interface;
    public function __construct($destination,$gateway,$subnet,$interface = null)
    {
        $this->destination = $destination;
        $this->gateway = $gateway;
        $this->subnet = $subnet;
        $this->interface = $interface;

    }

    public function __toString()
    {
        return 'Dest:'.$this->destination.PHP_EOL
            .'Gateway:'.$this->gateway.PHP_EOL
            .'Subnet:'.$this->subnet.PHP_EOL
            .'Interface:'.$this->interface;
    }

}