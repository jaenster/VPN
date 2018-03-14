<?php


namespace Socket;


use Kernel\Runnable;
use Kernel\Kernel;
use Socket\Interfaces\Connection;

abstract class Socket implements Runnable
{
    protected $ip,$port,$socket,$protocol,$className,$args;
    public function __construct(string $ip,int $port,string $className,int $protocol,array $args = [])
    {
        $this->ip = $ip;
        $this->port = $port;
        $this->className = $className;
        $this->protocol = $protocol;
        $this->args = $args;
        // What kind of type?
        switch ($protocol){
            case SOL_UDP:
                $type = SOCK_DGRAM;
                break;
            case SOL_TCP:
                $type = SOCK_STREAM;
                break;
            default:
                throw new \Exception('Unsupported protocol: '.$protocol);
        }
        // Create resource
        $this->socket =  socket_create(AF_INET, $type, $protocol);

        socket_set_nonblock($this->socket);

        // Register by the kernel as a "process"
        Kernel::register($this);

    }
    protected function createInstance($socket = null) : void{
        // In case we connect, its $this->socket we want to give
        if (!$socket = null) {
            $socket = $this->socket;
        }

        // should be a resource, if not, return
        if (!gettype($socket) == 'resource') {
            return;
        }

        // Create a reflection
        $reflection = (new \ReflectionClass($this->className));

        // Does the class implements the correct interface?
        if (!$reflection->implementsInterface(Connection::class))
        {
            throw new \Exception($this->className.' does not implement '.Connection::class);
        }

        $args = $this->args;
        array_unshift($args,$socket);
        // create a instance
        $reflection->newInstanceArgs($socket);
    }
}