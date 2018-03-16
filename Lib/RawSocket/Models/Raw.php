<?php
namespace Rawsocket\Model;


abstract class Raw
{
    protected $raw,     // the data of the model
        $hex=false,     // hex required to get it humanly readale
        $delimiter='.', // the delimiter to get it humanly readable
        $setter = null; // The function that needs to be called by __constructor

    public function __construct($argument=null)
    {
        // If $argument is null, skip
        if ($argument===null ){return;}

        // Set method name
        $methodName = $this->setter;

        // In case methodName is null
        if ($methodName == null){

            // Get name of class without namespace
            $methodName = 'set'.(new \ReflectionClass($this))->getShortName();

        }

        // Call if it exists
        if (method_exists($this, $methodName)) {
            $this->$methodName($argument);
        }
    }

    public function setRaw($raw) : self{
        $this->raw = $raw;
        return $this;
    }
    public function getRaw()
    {
        return $this->raw;
    }
    public function getNormal() : string
    {
        // Set return value
        $return = '';

        // Loop trough all chars
        foreach(str_split($this->raw) as $chr){

            // Return it to a "human" readable version
            if ($this->hex) {
                $number = ord($chr);
                if ($number < 16) {
                    $hex = '0'.dechex($number);
                } else {
                    $hex = dechex($number);
                }
                $return .= $this->delimiter.$hex;
            } else {
                $return .=$this->delimiter.ord($chr);
            }
        }

        // We start with a delimiter, remove it
        return substr($return,strlen($this->delimiter));
    }
    public function __toString() : string
    {
        return $this->getNormal();
    }

}