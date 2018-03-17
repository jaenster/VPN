<?php

namespace VPN\Transfer\Protocol;

use Rawsocket\Builder\IPv4Builder;
use Rawsocket\Exceptions\InvalidMacAddress;
use Rawsocket\Model\Protocol\IPv4;
use Rawsocket\Layer\Ethernet;
use Rawsocket\Pcap\DumpablePacket;
use VPN\Configuration\Conf;
use VPN\Daemon\Router\NetworkDevice;



class Protocol extends BaseProtocol
{

    const ROUTE_RESET=1,       ROUTE_DATA=2,       ROUTE_REQUEST=3;
    public function start() : void
    {
        // create a new instance of the routeHandler

        $header = '';

        // Let the other server know we are a new instance
        $header .= $this->pack(self::SYS_NEW_INSTANCE,$this->serverConfig->ip.':'.$this->serverConfig->port);
        // get the routes header
        $header .= $this->pack(self::SYS_ROUTES,$this->getRouteHeader());


        // Send the data
        $this->send(self::TYPE_SYSTEM,$header);


    }

    protected function parseIPv4(string $data) : void
    {
        $IPv4 = new IPv4($data);
        $ipDst = $IPv4->getDstIP();

        print $IPv4->getSrcIP()->getNormal().' -> '.$ipDst->getNormal().PHP_EOL;

        try {
            $macAddress =  Ethernet::getMacOfIP($ipDst);
            $device = Ethernet::getDevOfIP($ipDst);
        } catch (InvalidMacAddress $mac) {
            // No mac address found, we cant route it
            return;
        }


        $builder = new IPv4Builder(NetworkDevice::getNetworkInterfaceByDeviceName($device),$macAddress,$data);
        $builder->build();
        $builder->send();


    }
    public function parseRoute(array $msgs) : string
    {
        $return = '';
        foreach ($msgs as $msg)
        {
            $data = $msg['data'];
            switch ($msg['type'])
            {
                case self::ROUTE_RESET:
                    $this->serverConfig->routes->resetRoutes();
                    print 'Clearing Routes: '.$data.PHP_EOL;
                    break;

                case self::ROUTE_DATA:
                    print 'Adding route: '.$data.PHP_EOL;
                    $this->serverConfig->addRoute($data);
                    break;
                case self::ROUTE_REQUEST:
                    print 'Requested routes';
                    $return .= $this->getRouteHeader();
                    break;
            }
        }
        return $return;
    }
    public function getRouteHeader() : string
    {
        $return = $this->pack(self::ROUTE_RESET,'');
        foreach (explode(',',Conf::getEnv('myroutes')) as $route)
        {
            $return .= $this->pack(self::ROUTE_DATA,$route);
        }
        return $return;
    }


}

