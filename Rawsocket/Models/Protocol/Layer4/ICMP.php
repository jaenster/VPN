<?php

namespace Rawsocket\Models\Protocol\Layer4;




use Rawsocket\Layer\Layer3;
use Rawsocket\Layer\Layer4;
use Rawsocket\Model\Protocol\IPv4;
use Rawsocket\Model\Protocol\Protocol;

class ICMP extends Protocol implements Layer4
{
    protected $type,// Type of request (ping/pong/whatever)
            $code,
            $checksum,
            $identifier,
            $sequence,
            $payload,
            $ipv4;

    public function __construct(string $data,IPv4 $ipv4)
    {
        $this->payload = parent::__construct($data);

    }

    protected function protocolStructure(): array
    {
        return [
            'type'=>         ['bytes'=>1],
            'code'=>         ['bytes'=>1],
            'checksum'=>     ['bytes'=>2],
            'identifier'=>   ['bytes'=>2],
            'sequence'=>     ['bytes'=>2],
        ];
    }
    public function getType() : int{
        return ord($this->type);
    }
    public function getCode() : int{
        return ord($this->code);
    }
    public function getPayload() : string
    {
        return $this->payload;
    }
}