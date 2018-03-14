<?php



namespace Rawsocket\Layer;


use Rawsocket\Exceptions\InvalidIpAddress;
use Rawsocket\Model\IPAddress;
use Rawsocket\Model\IPv4Address;

class IPv4
{

    static function getIPFromInterface(string $dev) : IPv4Address
    {
        // get IP from device
        $output = shell_exec('/sbin/ifconfig '.$dev.' | grep \'inet addr:\' | cut -d: -f2 | awk \'{ print $1}\'');

        // remove the last char (since its a new line)
        $output = substr($output,0,-1);

        // Get the ip
        $ip = (new IPv4Address())->setIpFromString($output);

        // is it a ipv4 respons (should be)
        if (!$ip instanceof IPv4Address) {
            throw new InvalidIpAddress('on '.$dev.' '.$output);
        }

        // Return the ipv4 address
        return $ip;
    }
    static function getNetwork ($ip,$mask) {
        //convert ip addresses to long form
        $ip_address_long = ip2long($ip);
        $ip_nmask_long = ip2long($mask);

        //caculate network address
        $ip_net = $ip_address_long & $ip_nmask_long;

        //caculate first usable address
        $ip_host_first = ((~$ip_nmask_long) & $ip_address_long);
        $ip_first = ($ip_address_long ^ $ip_host_first) + 1;

        //caculate last usable address
        $ip_broadcast_invert = ~$ip_nmask_long;
        $ip_last = ($ip_address_long | $ip_broadcast_invert) - 1;

        //caculate broadcast address
        $ip_broadcast = $ip_address_long | $ip_broadcast_invert;

        //Output
        return [
            'network'=>long2ip($ip_net),
            'from'=>long2ip($ip_first),
            'to'=>long2ip($ip_last),
            'broadcast'=>long2ip($ip_broadcast)
        ];
    }

    static function mask2cidr($mask){
        $long = ip2long($mask);
        $base = ip2long('255.255.255.255');
        return 32-log(($long ^ $base)+1,2);

        /* xor-ing will give you the inverse mask,
            log base 2 of that +1 will return the number
            of bits that are off in the mask and subtracting
            from 32 gets you the cidr notation */
    }
    static function cidr2mask(int $netmask) : string {
        $netmask_result="";
        for($i=1; $i <= $netmask; $i++) {
            $netmask_result .= "1";
        }
        for($i=$netmask+1; $i <= 32; $i++) {
            $netmask_result .= "0";
        }
        $netmask_ip_binary_array = str_split( $netmask_result, 8 );
        $netmask_ip_decimal_array = array();
        foreach( $netmask_ip_binary_array as $k => $v ){
            $netmask_ip_decimal_array[$k] = bindec( $v ); // "100" => 4
        }
        $subnet = join( ".", $netmask_ip_decimal_array );
        return $subnet;
    }

    static function ipInRange($ip,$network,$mask)
    {
        $long = ip2long($ip);

        $data = self::getNetwork($network, $mask);

        return ($long >= ip2long($data['from']) && $long <= ip2long($data['to']));
    }




}