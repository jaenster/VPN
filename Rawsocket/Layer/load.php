<?php


use Rawsocket\Layer\Factory;
use Rawsocket\Model\Protocol\IPv4;
use Rawsocket\Model\Protocol\ARP;
use Rawsocket\Models\Protocol\Layer4\UDP;
use Rawsocket\Models\Protocol\Layer4\ICMP;
use Rawsocket\Models\Protocol\Layer4\TCP;

/*
 *  Classes that are registered as a specific class
 */

// Layer 3
Factory::registerLayer(3,IPv4::class,0x0800);
Factory::registerLayer(3,ARP::class,0x0806);

// Layer 4

Factory::registerLayer(4,ICMP::class,1);
Factory::registerLayer(4,TCP::class,6);
Factory::registerLayer(4,UDP::class,17);

