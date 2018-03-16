<?php

namespace VPN\Transfer\Protocol;


use VPN\Configuration\Conf;
use VPN\Kernel\Runnable;


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
    protected function parseSystemMsg(string $packet)
    {
        $returnPackage = '';
        $arr = $this->unpack($packet);
        foreach($arr as $msg)
        {
            switch ($msg['type'])
            {
                case self::SYS_PING: // Recved a ping
                    print 'Recvied a PING with payload: '.$msg['data'];
                    break;

                case self::SYS_PONG:
                    print 'Recvied a PONG with payload: '.$msg['data'];
                    break;

                case self::SYS_REQROUTES:
                    print 'Recvied a RoutesRequest'.PHP_EOL;
                    break;
                case self::SYS_ROUTES:
                    print 'Recvied routes'.PHP_EOL;
                    break;
                case self::SYS_PROXY: // Future plan
                    print 'TODO: Proxy!'.PHP_EOL;
                    break;
            }
        }
    }
    public function start() : void
    {
        // Request the other server's route
        $header = $this->pack('',self::SYS_REQROUTES);

        $header = $this->pack('',self::SYS_ROUTES);
    }
    public function run(): void
    {
        // ToDo: send a ping
    }

}

