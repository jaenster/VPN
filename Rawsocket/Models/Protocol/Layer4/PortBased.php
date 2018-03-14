<?php
namespace Rawsocket\Models\Protocol\Layer4;


use Rawsocket\Model\Protocol\IPv4;
use Rawsocket\Model\Protocol\Protocol;

abstract class PortBased extends Protocol
{
    protected $srcPort,
        $dstPort,
        $payload;

    public function __construct(string $data,IPv4 $IPv4)
    {
        $this->payload =  Parent::__construct($data);
    }

    public function getSrcPort() : Port
    {
        return $this->srcPort;
    }
    public function getDstPort() : Port
    {
        return $this->dstPort;
    }
}