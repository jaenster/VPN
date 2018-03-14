<?php

namespace Rawsocket\Model;

use Rawsocket\Model\Protocol\Ethernet;
use Rawsocket\Pcap\Packet as pcapPacket;

class Packet extends Raw
{
    protected $ethernet;
    protected $setter = 'setPacket';

    protected function setPacket(pcapPacket $packet) : self
    {
        $this->raw = $packet->getData();
        $this->ethernet = new Ethernet($this);
        return $this;
    }

    public function getEthernet() : Ethernet
    {
        return $this->ethernet;
    }



}