<?php

namespace Rawsocket\Model\Protocol;


use Rawsocket\Exceptions\InvalidInterface;
use Rawsocket\Exceptions\NotALayerInterface;
use Rawsocket\Exceptions\NotARegisteredLayer;
use Rawsocket\Layer\Factory;
use Rawsocket\Layer\Layer3;
use Rawsocket\Layer\Layer4;
use Rawsocket\Model\IPv4Address;
use Rawsocket\Model\Protocol\Type\ProtocolType;

class IPv4 extends Protocol implements Layer3
{


    protected $VersionHeader, $DSF, $totalLength, $identification, $flagsFragment, $TTL, $protocol, $headerChecksum,
    $srcIP, $dstIP, $layer4;

    public function __construct($data)
    {
        $payload = parent::__construct($data);


        // Get layer4
        try
        {
            $this->layer4 = Factory::getLayer4($this->protocol,$this, $payload);
        }
        catch (NotALayerInterface | NotARegisteredLayer $e )
        {
            //print 'Caught a unsupported IPv4 protocol: '. $this->protocol . PHP_EOL;
        }


    }

    protected function protocolStructure() : array
    {
        return  [
            'VersionHeader'     =>  ['bytes'=>1],
            'DSF'               => ['bytes'=>1],
            'totalLength'       => ['bytes'=>2],
            'identification'    =>['bytes'=>2],
            'flagsFragment'     =>['bytes'=>2],
            'TTL'               =>['bytes'=>1],
            'protocol'          =>['bytes'=>1,
                                    'to'=>ProtocolType::class],
            'headerChecksum'    =>['bytes'=>2],
            'srcIP'          =>['bytes'=>4,
                                    'to'=>IPv4Address::class],
            'dstIP'            =>['bytes'=>4,
                                    'to'=>IPv4Address::class],
        ];
    }

    public function getNextLayer(): Layer4
    {
        return $this->layer4;
    }
    public function getDstIP() : IPv4Address
    {
        return $this->dstIP;
    }
    public function getSrcIP() : IPv4Address
    {
        return $this->srcIP;
    }
}