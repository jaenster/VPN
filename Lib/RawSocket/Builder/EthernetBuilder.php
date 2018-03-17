<?php

namespace Rawsocket\Builder;


use Rawsocket\Model\MacAddress;
use Rawsocket\Model\Protocol\Type\EtherType;
use Rawsocket\Pcap\DumpablePacket;
use Rawsocket\Pcap\SimplePcap;

class EthernetBuilder
{
    protected $etherType,
        $macDst,
        $macSrc,
        $payload,
        $layerPayload,
        $network;

    public function __construct(NetworkInterface $network,MacAddress $macDst, EtherType $etherType)
    {
        $this->macDst = $macDst;
        $this->network = $network;
        $this->macSrc = $network->getMac();
        $this->etherType = $etherType;

    }

    public function build() : self
    {
        $this->payload = $this->getHeader().$this->layerPayload;
        return $this;
    }
    public function send() : self
    {
        // ToDo: Store somewhere a pcap device

        // Get simple Pcap device
        $simplePcap = $this->network->getSimplePcap();

        // The injection.
        $totalBytesSent = $simplePcap->send($this->payload);

        return $this;
    }
    private function getHeader() : string
    {
        $return  = $this->macDst->getRaw();
        $return  .= $this->macSrc->getRaw();
        $return  .= $this->etherType->getRawInt();

        return $return;
    }
    final protected function setEthernetPayload(string $payload) : void
    {
        $this->layerPayload = $payload;
    }



}