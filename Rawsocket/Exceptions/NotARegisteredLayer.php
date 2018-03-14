<?php

namespace Rawsocket\Exceptions;


class NotARegisteredLayer extends \Exception
{
    public function __construct($layer = '', $code = 0, \Throwable $previous = null)
    {
        parent::__construct('Not A Registered Layer ['.$layer.']', $code, $previous);
    }
}