<?php

namespace VPN\Transfer\Exceptions;


use Throwable;

class NoEncryptionSet extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct('No Encryption Set', $code, $previous);
    }
}