<?php


namespace VPN\Transfer\Encryption;


class NoEncryption implements Encryptable
{
    public function decrypt(string $data, string $paraphrase, string $seed): string
    {
        return $data;
    }
    public function encrypt(string $data, string $paraphrase, string $seed): string
    {
        return $data;
    }
}
