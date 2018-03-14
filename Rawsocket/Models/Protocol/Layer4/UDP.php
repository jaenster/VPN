<?php

namespace Rawsocket\Models\Protocol\Layer4;


use Rawsocket\Layer\Layer4;

class UDP extends PortBased implements Layer4
{
   protected $length,
        $checksum;

    protected function protocolStructure(): array
    {
        return [
          'srcPort'=>
              ['bytes'=>2,
                   'to'=>Port::class],
            'dstPort'=>
               ['bytes'=>2,
                   'to'=>Port::class],
            'length'=>
                ['bytes'=>2],
            'checksum' =>
                ['bytes'=>2]
        ];
    }
}