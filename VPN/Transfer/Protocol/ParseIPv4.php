<?php

namespace VPN\Transfer\Protocol;


use Rawsocket\Model\Protocol\IPv4;

trait ParseIPv4
{
    protected function parseIPv4(string $data)
    {
        $IPv4 = new IPv4($data);



        print $IPv4->getSrcIP()->getNormal().' -> '.$IPv4->getDstIP()->getNormal().PHP_EOL;
    }
}