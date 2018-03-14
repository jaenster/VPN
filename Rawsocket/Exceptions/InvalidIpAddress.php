<?php
namespace Rawsocket\Exceptions;


class InvalidIpAddress extends \Exception
{
    public function __construct($ip = '', $code = 0, \Throwable $previous = null)
    {
        parent::__construct('Invalid IP Address ['.$ip.']', $code, $previous);
    }
}