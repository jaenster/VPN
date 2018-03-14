<?php


namespace Rawsocket\Exceptions;


class NoSuchRoute extends \Exception
{
    public function __construct($Route = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct('No such route ['.$Route.']', $code, $previous);
    }
}