<?php
namespace VPN\Transfer\Encryption;
use Configuration\ServerConfig;

interface Encryptable
{
    public function __construct(ServerConfig $serverConfig);
    public function encrypt(string $data) : string;
    public function decrypt(string $data) : string;

}