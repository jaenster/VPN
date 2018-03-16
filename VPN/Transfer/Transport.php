<?php
namespace VPN\Transfer;

use VPN\Configuration\ServerConfig;
use VPN\Kernel\Kernel;
use VPN\Kernel\Runnable;
use Rawsocket\Pcap\DumpablePacket;
use VPN\Transfer\Encapsulation\Encapsulation;
use VPN\Transfer\Encryption\Encryptable;

final class Transport implements Runnable
{
    private $socket;
    public function __construct()
    {
        if(!($this->socket = socket_create(AF_INET, SOCK_DGRAM, 0)))
        {
            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);

            die("Couldn't create socket: [$errorcode] $errormsg \n");
        }

        // Non block it, and bind to a port
        socket_set_nonblock($this->socket);
        if (!socket_bind($this->socket,0,1337)) {
            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);
            die("Couldn't create socket: [$errorcode] $errormsg \n");
        }

        Kernel::register($this);

    }

    // Server function, recving data.
    public function run() : void
    {
        //Receive some data
        $r = socket_recvfrom($this->socket, $buf, 6144, 0, $remote_ip, $remote_port);
        if (!$r) {
            return; // No data
        }
        try {
            $serverConfig = ServerConfig::getByIP($remote_ip);
        } catch (\Exception $e) {
            return; // No such server known, drop packet
        }

        print new DumpablePacket($buf);
        // Let the protocol handle the recved msg
        $serverConfig->protocol->handleRecvPacket($buf);
    }
    public function start() : void
    {

    }


    public function send(string $data,ServerConfig $serverConfig)
    {

        print 'Sending data --- '.$serverConfig->ip.' -- '.$serverConfig->port.'.'.PHP_EOL;
        // Pack & Encrypt
        // sending data
        socket_sendto($this->socket, $data , strlen($data) , 0 , $serverConfig->ip , $serverConfig->port);
        print new DumpablePacket($data);

    }
    public function close()
    {
        socket_close($this->socket);
    }

}