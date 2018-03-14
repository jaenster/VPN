<?php

namespace Rawsocket\Models\Protocol\Layer4;


use Rawsocket\Layer\Layer4;

class TCP extends PortBased implements Layer4
{
    protected $sequence,
        $acknowledgment,
        $windowsSize,
        $checksum,
        $urgentPointer;

    protected function protocolStructure(): array
    {
        return [
            'srcPort'=>         ['bytes'=>2,
                                    'to'=>Port::class],
            'dstPort'=>         ['bytes'=>2,
                                    'to'=>Port::class],
            'sequence'=>        ['bytes'=>4],
            'acknowledgment'=>  ['bytes'=>4],
            'flags'         =>  ['bytes'=>4],
            'windowSize'    =>  ['bytes'=>2],
            'checksum'      =>  ['bytes'=>2],
            'urgentPointer' =>  ['bytes'=>2]
        ];
    }

}