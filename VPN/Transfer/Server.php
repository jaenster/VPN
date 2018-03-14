<?php
namespace VPN\Transfer;


use Configuration\Conf;

class Server extends Socket
{
    public function __construct($socket, $type)
    {
        parent::__construct($socket, $type);

        socket_getpeername($socket,$address);

        // Get server defaults for encapsulation
        $this->setEncapsulation(Conf::getEnv('Encapsulation'));

        // Get server defaults for encryption
        $this->setEncryption(Conf::getEnv('Encryption'));
    }
}