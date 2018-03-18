<?php

namespace VPN\Transfer\Protocol;

use Rawsocket\Pcap\DumpablePacket;
use VPN\Configuration\Conf;
use VPN\Configuration\ServerConfig;
use VPN\Kernel\Kernel;
use VPN\Kernel\Runnable;
use VPN\Transfer\Encapsulation\Encapsulation;
use VPN\Transfer\Encryption\Encryptable;

abstract class BaseProtocol implements Runnable
{
    public const  TYPE_SYSTEM = 1,      TYPE_IPv4 = 2,      TYPE_IPv6 = 3,
                  SYS_PING=1,           SYS_PONG=2,         SYS_NEW_INSTANCE=3,
                  SYS_ROUTES=4;
    private const ENCRYPT=0,            DECRYPT = 1,        PING_INTERVAL = 1; // Ping every x seconds;

    protected $serverConfig,$latency,$connectionUp = false;
    private $timer, $pingPayload,$waitForPong=false;

    final public function __construct(ServerConfig $serverConfig)
    {
        $this->serverConfig = $serverConfig;
        $this->timer = mTime();
        Kernel::register($this);
    }

    final public function run(): void
    {
        if (mTime()-$this->timer > self::PING_INTERVAL*1000)
        {
            // No response on previous ping request.
            if ($this->waitForPong) {
                $this->connectionUp = false;
            }
            $this->pingPayload = generateRandomString();
            $this->send(self::TYPE_SYSTEM,$this->pack(self::SYS_PING,$this->pingPayload));
            $this->timer = mTime();
            $this->waitForPong = true;
        }
    }

    final public function send(int $type,string $rawData) : void
    {
        Conf::getTransport()->send($this->packCrypt($type,$rawData),$this->serverConfig);
    }

    final public function pack(int $type,string $data) : string
    {
        return $this->serverConfig->getEncapsulation()->pack($type,$data);
    }

    final public function recv($rawData) : void
    {

        foreach ($this->unpackDecrypt($rawData) as $packet) {
            switch($packet['type'])
            {
                case self::TYPE_SYSTEM:
                    $this->parseSystemMsg($this->unpack($packet['data'])); // System msg
                    break;

                case self::TYPE_IPv4:
                    $this->parseIPv4($packet['data']);      // IPv4 msg
                    break;

                case self::TYPE_IPv6:
                    // IPv6 msg <--- for a future far away from here
            }
        }
        // Recved data, so line isn't dead. Reset ping timer
        /*
        if ($this->timer > self::PING_INTERVAL/2){
            $this->timer = mTime();
        }*/
    }
    private function parseSystemMsg(array $msgs) : void
    {
        $returnPackage = '';
        foreach($msgs as $msg)
        {
            switch ($msg['type'])
            {
                case self::SYS_PING: // Recved a ping
                    // Reply on PING
                    $returnPackage .=  $this->pack(self::SYS_PONG,$msg['data']);
                    break;

                case self::SYS_PONG:
                    if ($this->pingPayload != $msg['data']) {continue 2 ;}
                    $this->latency = (mTime()-$this->timer);

                    // Not waiting for ping anymore
                    $this->waitForPong = false;
                    $this->pingPayload = '';
                    $this->connectionUp = true;
                    print 'Latency with '.$this->serverConfig->ip.':'.$this->serverConfig->port.'  -- Round trip: '.$this->latency.PHP_EOL ;
                    break;

                case self::SYS_ROUTES:
                    // Let the routesHandler handle the routes
                    $routes = $this->parseRoute($this->unpack($msg['data']));
                    if (!empty($routes)) {
                        $returnPackage .= $this->pack(self::SYS_ROUTES, $routes);
                    }
                    break;

                case self::SYS_NEW_INSTANCE:
                    $returnPackage .= $this->pack(self::SYS_ROUTES,$this->getRouteHeader());

                    break;
            }
        }
        if (!empty($returnPackage))
        {
            $this->send(self::TYPE_SYSTEM,$returnPackage);
        }
    }
    private function packCrypt(int $type,string $rawData) : string
    {
        return $this->crypt(self::ENCRYPT,$this->pack($type,$rawData));
    }

    private function unpackDecrypt(string $rawData) : array
    {
        return $this->unpack($this->crypt(self::DECRYPT,$rawData));
    }

    private function unpack(string $data) : array
    {
        return $this->serverConfig->getEncapsulation()->unpack($data);
    }

    private function crypt(int $what,$data) : string
    {
        switch ($what)
        {
            case self::ENCRYPT:
                return $this->serverConfig->getEncryption()->encrypt($data);
            case self::DECRYPT:
                return $this->serverConfig->getEncryption()->decrypt($data);
        }
        throw new \Exception('Failed on [en/de]crypt');
    }

    abstract protected function parseRoute(array $msgs) : string;
    abstract protected function getRouteHeader() : string;
    abstract protected function parseIPv4(string $data) : void;
}