<?php


namespace VPN\Transfer\Encryption;

use Configuration\ServerConfig;

class NoEncryption implements Encryptable
{
    protected $serverConfig;
    public function __construct(ServerConfig $serverConfig)
    {
        $this->serverConfig = $serverConfig;
    }
    public function decrypt(string $data): string
    {
        return $data;
    }
    public function encrypt(string $data): string
    {
        return $data;
    }
}


