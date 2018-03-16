<?php

namespace Rawsocket\Builder;


use Rawsocket\Model\MacAddress;
use Rawsocket\Model\Protocol\Type\EtherType;

class IPv4Builder extends EthernetBuilder
{

    public function __construct(NetworkInterface $network, MacAddress $macDst, string $payload)
    {
        parent::__construct($network, $macDst, EtherType::get(0x0800));
        $this->setEthernetPayload($payload);
    }
}