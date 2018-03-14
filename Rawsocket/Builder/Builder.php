<?php

namespace Rawsocket\Builder;


use Rawsocket\Model\IPAddress;
use Rawsocket\Model\MacAddress;

abstract class Builder
{
    protected $protocol,$macDst,$macSrc,$ipSrc,$ipDst;


    public function __construct(){

    }


    protected function setProtocol(Protocol $protocol)
    {
        $this->protocol = $protocol;
    }

}