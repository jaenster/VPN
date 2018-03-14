<?php

namespace Rawsocket\Model\Protocol;

use Rawsocket\Layer\Layer3;
use Rawsocket\Layer\Layer4;
use Rawsocket\Model\IPv4Address;
use Rawsocket\Model\MacAddress;

class ARP extends Protocol implements Layer3
{

    public const OPCode_reply = 0x0002;
    public const OPCode_request = 0x0001;

    protected $hardwareType, // (always) Ethernet
        $IPVersion, // Ip version, 4 or 6
        $hardwareSize, // Hardware size, always 6 (macadress = 6 digits long)
        $protocolSize, // Protocol size, same as ip version, 4 or 6,
        $opcode, // Is it a request or reply?
        $srcMac, // The device that given the ARP reply/request
        $targetMac, // the requested data
        $srcIP, // Source ip
        $targetIP; // Targed ip (requested data)

    public function __construct($payload)
    {
        Parent::__construct($payload);
        $this->opcode = hexdec(bin2hex($this->opcode));
    }

    public function getNextLayer(): Layer4
    {
        throw new \Exception('Last Layer');
    }

    public function __toString()
    {
        switch (true){
            case !!($this->opcode & self::OPCode_request) == self::OPCode_request:
                return 'ARP | '.$this->targetIP. ' is at '.$this->srcMac;
                break;
            case !!($this->opcode & self::OPCode_reply) == self::OPCode_reply:
                // This is a reply:
                return 'ARP | Who has '.$this->targetIP.'? Tell '.$this->srcMac;
                break;
        }
        return '';
    }
    protected function protocolStructure() : array
    {
        return  [
            'hardwareType' => ['bytes'=>2],
            'IPVersion' => ['bytes'=>2],
            'hardwareSize' => ['bytes'=>1],
            'protocolSize' => ['bytes'=>1],
            'opcode' => ['bytes'=>2],
            'srcMac' =>
                ['bytes'=> 6,
                    'to'=>MacAddress::class],
            'srcIP' =>
                ['bytes'=> 4,
                    'to'=>IPv4Address::class],
            'targetMac' =>
                ['bytes'=> 6,
                    'to'=>MacAddress::class],
            'targetIP' =>
                ['bytes'=> 4,
                    'to'=>IPv4Address::class],

        ];
    }
    public function getOPCode() : int
    {
        return $this->opcode;
    }
    public function getSrcMac() : MacAddress
    {
        return $this->srcMac;
    }
    public function getTargetMac() : MacAddress
    {
        return $this->targetMac;
    }

    public function getSrcIP() : IPv4Address
    {
        return $this->srcIP;
    }
    public function getTargetIP() : IPv4Address
    {
        return $this->targetIP;
    }
}