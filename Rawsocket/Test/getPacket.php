<?php

namespace Test;

use Rawsocket\Builder\ARPBuilder;

require_once '../includor.php';


use Rawsocket\Model\Packet;
use Rawsocket\Pcap\SimplePcap;
use Rawsocket\Pcap\DumpablePacket;

$simplePcap = new SimplePcap('eth2', '',4096, 0);
print 'start'.PHP_EOL;
/* Main sniffing loop */

while (true) {
    $packet = $simplePcap->get();
    //print new DumpablePacket($packet) . PHP_EOL;
    new Packet($packet);
}

