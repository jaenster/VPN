<?php
namespace VPN\Transfer\Encryption;
interface Encryptable
{
    public function encrypt(string $data,string $paraphrase,string $seed) : string;
    public function decrypt(string $data,string $paraphrase,string $seed) : string;

}