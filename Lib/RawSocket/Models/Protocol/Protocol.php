<?php

namespace Rawsocket\Model\Protocol;
use \ReflectionClass;

abstract class Protocol
{

    abstract protected function protocolStructure() : array;
    public function __construct(string $data)
    {
        // Protocol design isn't set
        foreach ($this->protocolStructure() as $key=> $value){

            // Keep it readable, get values
            $start = 0;
            if (isset($value['offset'])) {
                $start  = $value['offset'];
            }

            $end    = $value['bytes'];

            // get substr and remove
            $this->$key = $this->substrPop($data,$start,$end);


            // Convert it to a object
            if (isset($value['to'])) {
                $this->$key = (new ReflectionClass($value['to']))->newInstance($this->$key);
            }
        }
        return $data;
    }

    private function substrPop(&$data,$start,$end) : string
    {
        $result = substr($data,$start,$end);
        $data = substr($data,$start+$end);
        return $result;

    }

}