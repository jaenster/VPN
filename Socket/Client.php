<?php
namespace Socket;


use Kernel\Kernel;
use Kernel\Runnable;

class Client extends Socket
{
    protected $up,$connectionStarted,$timestamp;


    public function start() : void
    {
        print 'Connect first time! -- '.$this->ip.PHP_EOL;
        $this->connect();
    }
    public function run() : void{
        // Connection up?
        if ($this->up == true) {
            $this->up = socket_last_error($this->socket) === 0;
            return;
        }

        $errorcode = socket_last_error();
        switch ($errorcode)
        {
            case 0:
                // Successfully connected
                //Connection up. Create class
                $this->createInstance();

                $this->up = true;
                break;
            case 115:
                return; // Connection in progress.
            default:
                print 'Errorcode :'.$errorcode.PHP_EOL;
                print socket_strerror($errorcode).PHP_EOL;
                break;
        }
        // Reconnect?
        if (time() - $this->timestamp > 60) {
            print 'Reconnect!'.PHP_EOL;
            $this->connect();
        }
    }

    private function connect() {
        //print '(re)connect to '.$this->ip.':'.$this->port.PHP_EOL;
        socket_connect($this->socket,gethostbyname($this->ip),$this->port);
        $this->timestamp = time(); // get the timestamp
    }
    private function isStillUp()
    {

    }

}