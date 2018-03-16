<?php

namespace Rawsocket\Builder;

/*
 * Class that builds a ARP packet
 */


use Rawsocket\Model\IPv4Address;
use Rawsocket\Model\MacAddress;
use Rawsocket\Model\Protocol\Type\EtherType;
use Rawsocket\Model\Protocol\ARP as ARPModel;

class ARPBuilder extends EthernetBuilder
{
    protected $otherMac,
        $otherIP,
        $network,
        $OPCode;
    public function __construct(NetworkInterface $network, MacAddress $otherMac, IPv4Address $otherIP, IPv4Address $requestIP, $OPCode)
    {
        $this->otherMac = $otherMac;
        $this->otherIP = $otherIP;
        $this->network = $network;
        $this->OPCode = $OPCode;


        // Create a EtherType object
        $EtherType = (new EtherType())->setAsInt(0x0806);

        // This test always goes trough, however PHPStorm now accepts it as a EtherType from here on
        if (!$EtherType instanceof EtherType) {
            return ;
        }

        parent::__construct($network,      // Our (fake) interface
                            $otherMac,    // The Other mac address
                            $EtherType); // EtherType of ARP (0x0806)
    }


    public function build(): EthernetBuilder
    {
        $payload = $this->getHeader($this->OPCode);
        switch ($this->OPCode) {
            // Send a request [ Who has TargetIP ? Tell srcMac ]
            case !!($this->OPCode & ARPModel::OPCode_request) == ARPModel::OPCode_request:
                $payload .= $this->network->getMac()->getRaw();   // Sender Mac, from our (fake) network interface
                $payload .= $this->network->getIPv4()->getRaw();  // Sender IP,  from our (fake) network interface
                $payload .= $this->otherMac->getRaw();            // The Mac we care about
                $payload .= $this->otherIP->getRaw();             // The IP we care about
                break;
            // Send a reply [ targetIP is at: srcMac ]
            case !!($this->OPCode & ARPModel::OPCode_reply) == ARPModel::OPCode_reply:
                $payload .= $this->network->getMac()->getRaw();   // Sender Mac, from our (fake) network interface
                $payload .= $this->network->getIPv4()->getRaw();  // Sender IP,  from our (fake) network interface
                $payload .= $this->otherMac->getRaw();            // The Mac we care about
                $payload .= $this->otherIP->getRaw();             // The IP we care about
                break;
        }

        // Give the payload we made to the Ethernet
        $this->setEthernetPayload($payload);
        parent::build();
        return $this;
    }

    private function getHeader(int $opcode) : string
    {
        $return  = chr(0).chr(1); // Ethernet flag
        $return .= chr(8).chr(0);     // Typical flag for IPv4 - 0x0800
        $return .= chr(6);              // Hardware size (Length of a macAddress, always 6)
        $return .= chr(4);              // Protocol size, same as IPv4 (4 bytes for the ip)
        $return .= chr(0).chr($opcode); // The opcode (Is it a request or reply?)
        return $return;
    }
}