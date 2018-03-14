<?php

namespace Socket\Interfaces;


interface Connection
{
    public function __construct(resource $socket);
}