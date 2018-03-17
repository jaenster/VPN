<?php

namespace Rawsocket\Model\Protocol;


use Rawsocket\Exceptions\NotALayerInterface;
use Rawsocket\Exceptions\NotARegisteredLayer;
use Rawsocket\Layer\Factory;
use Rawsocket\Layer\Layer2;
use Rawsocket\Layer\Layer3;
use Rawsocket\Model\Protocol\Type\EtherType;
use Rawsocket\Model\Packet;
use Rawsocket\Model\MacAddress;


class Ethernet extends Protocol implements Layer2
{
    protected $macDst,
    $macSrc,
    $etherType,
    $layer3;

    public function __construct(Packet $packet)
    {
        // Get raw data
        $data = $packet->getRaw();

        // Parse Protocol Design
        $payload = parent::__construct($data);

        // Get layer3
        try {
            $this->layer3 = Factory::getLayer3($this->etherType, $payload);
        } catch (NotALayerInterface | NotARegisteredLayer $e) {
            return ; // No next layer
        }
    }


    public function getNextLayer(): Layer3
    {
        if (!$this->layer3 instanceof Layer3){
            throw new NotALayerInterface('Not a layer3 interface');
        }
        return $this->layer3;
    }

    protected function protocolStructure() : array
    {
        return  [
            'macDst' =>
                ['bytes'=>6,
                 'to'=>MacAddress::class],
            'macSrc' =>
                ['bytes'=>6,
                 'to'=>MacAddress::class],
            'etherType' =>
                ['bytes'=>2,
                   'to'=>EtherType::class],
        ];
    }

    // Getters
    public function getMacSrc() : MacAddress
    {
        return $this->macSrc;
    }
    public function getMacDst() : MacAddress
    {
        return $this->macDst;
    }
}