<?php

namespace Rawsocket\Layer;

use Rawsocket\Exceptions\NotALayerInterface;
use Rawsocket\Exceptions\NotARegisteredLayer;
use Rawsocket\Model\Protocol\Type\EtherType;
use Rawsocket\Model\Protocol\Type\ProtocolType;
use \ReflectionClass;

class Factory
{
    private static $layer = [];

    public static function registerLayer(int $Level,string $ClassName,int $Type) : void{
        self::$layer[$Level][$Type] = $ClassName;
    }


    public static function getLayer3(EtherType $EtherType, string $payload) : Layer3
    {

        // Error handing, can we find it in the array?
        if (!isset(self::$layer[3][$EtherType->getRaw()])){
            throw new NotARegisteredLayer('Lvl 3 - '.$EtherType->getRaw());
        }

        // Does this class implements Layer3? (or exist?)
        $reflection = (new ReflectionClass(self::$layer[3][$EtherType->getRaw()]));

        // Create a new instance.
        $obj = $reflection->newInstance($payload);

        // Error handing, is an object of Layer3, otherwise we cant return it
        if (! $reflection->implementsInterface(Layer3::class)
            || !$obj instanceof Layer3)
        {
            throw new NotALayerInterface('Layer 3 - '.self::$layer[3][$EtherType->getRaw()]);
        }
        return $obj;
    }


    public static function getLayer4(ProtocolType $ProtocolType,Layer3 $layer3, string $payload) : Layer4
    {
        // Error handing, can we find it in the array?
        if (!isset(self::$layer[4][$ProtocolType->getRaw()])){
            throw new NotARegisteredLayer('Lvl 4 - '.$ProtocolType->getRaw());
        }

        // Does this class implements Layer4? (or exist?)
        $reflection = (new ReflectionClass(self::$layer[4][$ProtocolType->getRaw()]));

        // Create a new instance.
        $obj = $reflection->newInstance($payload,$layer3);

        // Error handing, is an object of Layer3, otherwise we cant return it
        if (!$obj instanceof Layer4)
        {
            throw new NotALayerInterface('Layer 4 - '.self::$layer[4][$ProtocolType->getRaw()]);
        }
        return $obj;

    }

}