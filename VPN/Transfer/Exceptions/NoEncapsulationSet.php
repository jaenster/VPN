<?php

namespace VPN\Transfer\Exceptions;


use Throwable;

class NoEncapsulationSet extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct('No such Encapsulation Set', $code, $previous);
    }
}