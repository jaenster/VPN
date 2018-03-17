<?php

namespace VPN\Transfer\Encapsulation;


// Simple Encapsulation.
// First 2 bytes are the size
// the rest is the payload



class SimpleEncapsulation implements Encapsulation
{

    public function pack(int $type,string $data) : string
    {
        // Error handling. If bigger then 65536 it doesn't fit in 2 bytes
        if (strlen($data) > 4095) {
            throw new \Exception('Too big of a packet');
        }
        if (strlen($type) > 16) {
            throw new \Exception('Type too big');
        }

        // Header, size (2 bytes)
        $return = self::generateHeader(strlen($data),$type);
        // add the data
        $return .= $data;

        return $return;
    }

    public function unpack(string $data) : array
    {
        // the incomplete data
        $return = [];

        // Loop as long we have data
        while (strlen($data) != 0){
            // Get length of the packet
            $str = substr($data,0,2);

            $arr = self::parseHeader($str);
            $length = $arr[0];

            if (strlen($data)-2 < $length){
                //$return['incomplete'] = $data;
                break;
            }
            // get packet
            $packet = substr($data,2,$length);

            // Remove last packet from
            $data = substr($data,2+$length);

            $return[] = ['data'=>$packet,'type'=>$arr['type']];
        }
        return $return;
    }

    private function generateHeader(int $var,int $type) : string
    {
        $bytes=2;

        // Convert to hex
        $hex = dechex($var); // in case the number is too long to store 12 bits

        // add padding zero if needed
        if (strlen($hex) < (($bytes*2)-1))
        {
            $hex = str_repeat('0',(($bytes*2)-1)-strlen($hex)).$hex;
        }

        $hex = ((string) dechex($type)).((string) $hex);

        $return = '';
        // Loop trough the hex and make chr's from it, so a binary
        for($i=0;$i<strlen($hex);$i+=2){

            // get chr from the 2 hex's
            $return .= chr(hexdec(substr($hex,$i,2)));
        }

        return $return;

    }


    private function parseHeader($binary)
    {
        // Get the first char & hex it
        $firstchar_hex = dechex(ord(substr($binary,0,1)));

        // Error handling, only one char came out of the hex? Make it 2
        if (strlen($firstchar_hex) == 1) {
            $firstchar_hex .= '0'.(string) $firstchar_hex;
        }

        // get the type
        $type = hexdec(substr($firstchar_hex,0,1));

        $binary = chr(hexdec('0'.substr($firstchar_hex,1,1))).substr($binary,1);

        $value = 0;
        $i=0;
        foreach (array_reverse(str_split($binary)) as $l){
            $value += max(1,(256*$i))* ord($l);;
            $i++;
        }
        return [$value,'type'=>$type];
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

        return substr($return,1,0);

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