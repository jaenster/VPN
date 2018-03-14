<?php

namespace VPN\Transfer\Encapsulation;


// Simple Encapsulation.
// First 2 bytes are the size
// the rest is the payload


class Simple implements Encapsulation
{

    public function pack(string $data) : string
    {
        // Error handling. If bigger then 65536 it doesn't fit in 2 bytes
        if (strlen($data) > 65536) {
            throw new \Exception('Too big of a packet');
        }

        // Header, size (2 bytes)
        $return = self::long2Binary(strlen($data),2);

        // add the data
        $return .= $data;

        return $return;
    }

    public function unpack(string $data) : array
    {
        // the incomplete data
        $return['incomplete'] = '';

        // Loop as long we have data
        while (strlen($data) != 0){
            // Get length of the packet
            $str = substr($data,0,2);

            $length = self::binary2Long($str);

            if (strlen($data)-2 < $length){
                $return['incomplete'] = $data;
                break;
            }
            // get packet & gzuncompress it
            $packet = substr($data,2,$length);

            // Remove last packet from
            $data = substr($data,2+$length);

            $return[] = $packet;
        }


        //$return['left'] = $packet;
        return $return;
    }


    private function long2Binary(int $var,$bytes=2) : string
    {
        // Convert to hex
        $hex = dechex($var);

        // odd? add padding zero
        if (strlen($hex) % 2){
            $hex = '0'.$hex;
        }

        $return = '';

        for($i=0;$i<strlen($hex);$i+=2){

            // get chr from the 2 hex's
            $return .= chr(hexdec(substr($hex,$i,2)));
        }
        if ($bytes > strlen($return)) {
            $return = str_repeat(chr(0),$bytes-strlen($return)).$return;
        }
        return $return;

    }



    private function binary2Long($binary)
    {
        $value = 0;
        $i=0;
        foreach (array_reverse(str_split($binary)) as $l){
            $value += max(1,(256*$i))* ord($l);;
            $i++;
        }
        return $value;
    }

}