<?php

namespace VPN\Transfer\Encapsulation;

interface Encapsulation
{
    public function pack(string $str,int $type) : string;
    public function unpack(string $str) : array;
}