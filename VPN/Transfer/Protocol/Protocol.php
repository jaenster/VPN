<?php

namespace VPN\Transfer\Protocol;


use VPN\Configuration\ServerConfig;
use VPN\Kernel\Kernel;
use VPN\Kernel\Runnable;
use VPN\Transfer\Encapsulation\Encapsulation;
use VPN\Transfer\Encryption\Encryptable;

abstract class Protocol implements Runnable
{
    public const TYPE_SYSTEM = 1,TYPE_IPv4 = 2, TYPE_IPv6 = 3;
    private const ENCRYPT=0,DECRYPT = 1;
    public const SYS_PING=1, SYS_PONG=2,SYS_REQROUTES=3, SYS_ROUTES=4,SYS_PROXY=5;

    public $serverConfig,$ping,$latancy;
    public $connectionUp = false;
    final public function __construct(ServerConfig $serverConfig)
    {
        // set the server config
        $this->serverConfig = $serverConfig;

        // Make ping object
        $this->ping = new Ping($this);

        Kernel::register($this);
    }
    abstract public function handleRecvPacket(string $rawData) : void;
    abstract public function handleSendPacket(string $rawData,int $type) : void;

    protected function packCrypt(string $rawData,int $type) : string
    {

        return $this->crypt(self::ENCRYPT,$this->pack($rawData,$type));
    }

    protected function unpackDecrypt(string $rawData) : array
    {
        return $this->unpack($this->crypt(self::DECRYPT,$rawData));
    }

    public function pack(string $data, int $type) : string
    {
        return $this->getEncapsulation()->pack($data,$type);
    }

    protected function unpack(string $data) : array
    {
        return $this->getEncapsulation()->unpack($data);
    }

    private function getEncapsulation() : encapsulation
    {
        $encap = $this->serverConfig->encapsulation;
        if (!$encap instanceof Encapsulation)   {throw new \Exception('No such Encapsulation');}
        return $encap;
    }

    private function crypt(int $what,$data) : string
    {
        $encryption = $this->serverConfig->encryption;
        if (!$encryption instanceof Encryptable){throw new \Exception('No such Encryption');}

        switch ($what)
        {
            case self::ENCRYPT:
                return $encryption->encrypt($data);
            case self::DECRYPT:
                return $encryption->decrypt($data);
        }
        throw new \Exception('Failed on [en/de]crypt');
    }
}