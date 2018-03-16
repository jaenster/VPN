<?php

namespace Rawsocket\Model;


use Rawsocket\Exceptions\InvalidMacAddress;

class MacAddress extends Raw
{
    protected $hex = true,
        $delimiter = ':';

    public function setAsHex(string $hex) : self{
        // reset stored mac address.
        if (!empty($this->raw)){
            $this->raw = '';
        }

        // Remove all kind of padding of any kind
        $hex = preg_replace("/[^a-fA-F0-9]/", "", $hex);

        // Error checking
        if (!strlen($hex) == 12) {
            throw new InvalidMacAddress($hex);
        }

        // Loop trough them all
        for($i=0;$i<strlen($hex);$i+=2){
            // Convert to hex, and set the variable
            $this->raw .= chr(hexdec(substr($hex,$i,2)));
        }
        return $this;
    }

    protected function setMacAddress(string $raw) : self {
        $this->raw = $raw;
        return $this;
    }

}