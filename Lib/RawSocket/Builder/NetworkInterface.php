<?php

namespace Rawsocket\Builder;


use Rawsocket\Model\IPv4Address;
use Rawsocket\Model\MacAddress;
use Rawsocket\Pcap\SimplePcap;

interface NetworkInterface
{
    public function getMac() : MacAddress;
    public function getIPv4() : IPv4Address;
    public function getDeviceName() : string;
    public function getSimplePcap() : SimplePcap;

}