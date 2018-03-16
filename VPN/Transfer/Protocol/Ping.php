<?php


namespace VPN\Transfer\Protocol;


class Ping
{
    const INTERVAL = 5; // Ping every 5 seconds

    public  $steady = false,
            $timer;

    private $protocol,$payload,$waitForPong=false;

    public function __construct(Protocol $p)
    {
        $this->protocol = $p;
        $this->timer = $this->microtime ();
    }


    public function pingTime() : void
    {
        if ($this->microtime()-$this->timer > self::INTERVAL*1000)
        {
            if ($this->waitForPong)
            {
                // No response on previous ping request.
                $this->protocol->connectionUp = false;
            }
            $this->payload = $this->generateRandomString();
            $this->protocol->handleSendPacket($this->protocol->pack($this->payload,Protocol::SYS_PING),Protocol::TYPE_SYSTEM);
            $this->timer = $this->microtime ();
            $this->waitForPong = true;
        }
    }

    public function replyPing($ping) : string
    {
        return $this->protocol->pack($ping,Protocol::SYS_PONG);
    }

    public function gotPong($pong) : void
    {

        // Got a incorrect pong?
        if ($this->payload != $pong) {return ;}
        $this->protocol->latancy = ($this->microtime()-$this->timer);
        // Not waiting for ping anymore
        $this->waitForPong = false;
        $this->payload = '';
        $this->protocol->connectionUp = true;
        print 'Latency with '.$this->protocol->serverConfig->ip.':'.$this->protocol->serverConfig->port.'  -- Round trip: '.$this->protocol->latancy.PHP_EOL ;
    }

    private function generateRandomString($length = 30) : string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    private function microtime()
    {
        return (microtime(true)*1000);
    }

}