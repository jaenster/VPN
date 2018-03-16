<?php


namespace Rawsocket\Model\Protocol\Type;

class EtherType extends BinaryInt
{
    public static function get(int $var) : EtherType
    {
        $ethertype = new EtherType();
        $ethertype->setAsInt($var);
        return $ethertype;
    }

}