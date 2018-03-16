<?php

namespace Rawsocket\Model;



abstract class IPAddress extends raw
{
    protected $setter = 'setIp';
    abstract public function setIP(string $ip) : self;
}