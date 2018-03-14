<?php
namespace Socket;


use Kernel\Kernel;

class Client extends Socket
{
    protected $up,$connectionStarted;

    public function start() : void
    {
        // Create socket
        try {
            $this->socket = socket_create(AF_INET,SOCK_DGRAM,SOL_UDP);
            socket_set_nonblock($this->socket);
        } catch (\Throwable  $e) {
            return;
        }
        $this->connect();
    }
    public function run() : void{
        // Connection up?
        $errorcode = socket_last_error();
        if ($errorcode === 0 ){

            //Connection up. Create class
            $this->createInstance();

            // deregister by kernel
            Kernel::detach($this);
        }
    }

    private function connect() {
        socket_connect($this->socket,gethostbyname($this->ip),$this->port);
        $this->connectionStarted = true;
    }

}