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
                    $this->parseSystemMsg($packet['data']); // System msg
                    break;
                case self::TYPE_IPv4:
                    $this->parseIPv4($packet['data']);      // IPv4 msg
                    break;
                case self::TYPE_IPv6:
                    // IPv6 msg <--- for a future far away from here
            }
        }

    }
    public function handleSendPacket(string $rawData,int $type) : void
    {
        $this->send($rawData,$type);
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

                    // Reply on PING
                    $returnPackage .= $this->ping->replyPing($msg['data']);
                    break;

                case self::SYS_PONG:
                    $this->ping->gotPong($msg['data']);
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

        if ($returnPackage != '')
        {
            $this->send($returnPackage,self::TYPE_SYSTEM);
        }
    }
    public function start() : void
    {
        $header = '';

        // Request the other server's route
        $header .= $this->pack('',self::SYS_REQROUTES);

        // Send our routes
        //$header .= $this->pack('',self::SYS_ROUTES);

        // Send the data
        $this->send($header,self::TYPE_SYSTEM);

    }
    public function run(): void
    {
        // Send a ping, if needed
        $this->ping->pingTime();
    }
    private function send($rawData,$type) : void
    {
        Conf::getTransport()->send($this->packCrypt($rawData,$type),$this->serverConfig);
    }
}

