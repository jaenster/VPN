<?php

namespace Rawsocket\Exceptions;


class InvalidInterface extends \Exception
{
    public function __construct($dev = '', $code = 0, \Throwable $previous = null)
    {
        parent::__construct('Invalid Interface ['.$dev.']', $code, $previous);
    }
}