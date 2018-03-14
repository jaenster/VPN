<?php

namespace Rawsocket\Model;


class Payload extends raw
{
    protected $setter = 'setPayload';

    public function setPayload($raw){
        $this->raw = $raw;
    }
}