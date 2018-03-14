<?php

namespace Rawsocket\Model\Protocol\Type;


use Rawsocket\Model\Raw;

class BinaryInt extends Raw
{
    protected $hex = true,
        $delimiter = '',
        $setter = 'setType';

    public function setType(string $data){
        // Get a decimal
        $this->raw = hexdec(bin2hex($data));
    }
    public function __toString(): string
    {
        return $this->raw;
    }
    public function setAsInt(int $int) : self
    {
        $this->raw = $int;
        return $this;
    }
    public function getRawInt()
    {
        $hex = dechex($this->raw);
        if ($hex % 2 == 0 ){
            $hex = '0'.$hex;
        }
        print $hex.PHP_EOL;
        return hex2bin($hex);
    }
}