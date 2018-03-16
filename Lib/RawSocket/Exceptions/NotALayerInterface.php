<?php

namespace Rawsocket\Exceptions;


class NotALayerInterface extends \Exception
{
    public function __construct($layer = '', $code = 0, \Throwable $previous = null)
    {
        parent::__construct('Not A Layer 3 interface ['.$layer.']', $code, $previous);
    }
}