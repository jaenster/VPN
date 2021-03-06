<?php

namespace VPN\Transfer\Encapsulation;

interface Encapsulation
{
    public function pack(int $type,string $str) : string;
    public function unpack(string $str) : array;
}