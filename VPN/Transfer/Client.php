<?php

namespace VPN\Transfer;

use Configuration\ServerConfig;
use Rawsocket\Model\Packet;

class Client extends Socket{
    public function __construct(resource $socket,int $type,ServerConfig $server){
        parent::__construct($socket,$type);

        $this->setEncapsulation($server->encapsulation);

        $this->setEncryption($server->encryption);

        $server->setClient($this);
    }

    public function sendPacket(Packet $packet)
    {
        $this->send($packet->getRaw());
    }
}