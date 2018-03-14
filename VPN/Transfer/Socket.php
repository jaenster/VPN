<?php


namespace VPN\Daemon\Transfer;


use Socket\Interfaces\Connection;
use VPN\Transfer\Encapsulation\Encapsulation;
use VPN\Transfer\Encryption\Encryptable;
use VPN\Transfer\Exceptions\NoEncapsulationSet;
use VPN\Transfer\Exceptions\NoEncryptionSet;

/**
 * Class Socket
 * @package VPN\Daemon\Transfer
 */

class Socket implements Connection
{
    protected $socket,$encryption,$type,$encapsulation;
    public function __construct(resource $socket,int $type)
    {
        $this->socket = $socket;
        $this->type = $type;
    }
    public function send(string $data) : void
    {
        // Error handling
        if (!$this->encapsulation instanceof Encapsulation)
        {
            throw new NoEncapsulationSet();
        }
        // Error handling
        if (!$this->encryption instanceof Encryptable)
        {
            throw new NoEncryptionSet();
        }

        //$this->encapsulation->pack($this->encryption->encrypt($data));
        switch ($this->type){
            case SOL_UDP:
                break;
            case SOL_TCP:
                break;
        }
    }
    public function setEncapsulation(Encapsulation $encapsulation)
    {
        $this->encapsulation;

    }
    public function setEncryption(Encryptable $e){
        $this->encryption = $e;
    }




}