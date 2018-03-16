<?php

namespace VPN\Transfer\Protocol;


use Configuration\Conf;


class Protocolv0_1 extends Protocol
{
    use ParseIPv4;
    public function handleRecvPacket($rawData) : void
    {
        foreach ($this->unpackDecrypt($rawData) as $packet) {
            switch($packet['type'])
            {
                case self::TYPE_SYSTEM:
                    // System msg
                case self::TYPE_IPv4:
                    $this->parseIPv4($packet['data']);
                    // IPv4 msg
                case self::TYPE_IPv6:
                    // IPv6 msg <--- for a future far away from here
            }
        }

    }
    public function handleSendPacket(string $rawData,int $type) : void
    {
        Conf::getTransport()->send($this->packCrypt($rawData,$type),$this->serverConfig);
    }


}