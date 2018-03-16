<?php
namespace Rawsocket\Exceptions;


use Throwable;

class InvalidMacAddress extends \Exception
{
    public function __construct($mac = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct('Invalid Mac Address ['.$mac.']', $code, $previous);
    }
}