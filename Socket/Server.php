<?php

namespace Socket;

use Kernel\Runnable;
use Kernel\Kernel;
use Socket\Interfaces\Connection;

class Server extends Socket
{

    public function start() : void
    {
        // Bind
        socket_bind($this->socket,$this->ip,$this->port);
        // Listen
        if ($this->protocol === SOL_TCP) {
            socket_listen($this->socket,SOMAXCONN);
        }
    }
    public function run() : void{
        // accept new clients;
        $newc = socket_accept($this->socket);
        if (!gettype($newc) === 'resource' ){
            return;
        }

        try {
            $this->createInstance($newc);
        } catch (\Exception $e)
        {
            print $e->getMessage().PHP_EOL;
        }
    }
}