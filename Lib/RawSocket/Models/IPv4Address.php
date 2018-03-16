<?php

namespace Rawsocket\Model;


use Rawsocket\Exceptions\InvalidIpAddress;

class IPv4Address extends IPAddress
{

    public function setIP(string $raw) : IPAddress {
        $this->raw = $raw;
        return $this;
    }
    public function setIpFromString($ip) : IPAddress {
        // get A.B.C.D in a array as 0,1,2,3
        $arr = explode('.',$ip);

        // Error handing, it should return a array with 4 elements
        if (!count($arr) === 4){
            throw new InvalidIpAddress($ip);
        }

        // Reset previous stored ip.
        $this->raw = '';

        // Loop trough all the classes of ip, so A.B.C.D
        foreach ($arr as $class){

            // Error handling, it should be pure digits and not exceed 255
            if (!ctype_digit($class) || (int) $class > 255) {
                throw new InvalidIpAddress($ip);
            }

            // Append the chr to the raw
            $this->raw .= chr($class);
        }

        return $this;
    }

}