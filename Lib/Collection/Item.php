<?php


namespace VPN\Lib\Collection;

/**
 * Class Item
 * @package Collection
 * @property object $obj
 */

abstract class Item
{
    protected  $data;
    public function __construct(object $obj)
    {
        $this->data = ['obj'=>$obj];
    }

    public function __set($name, $value) : void
    {
        if ($name === 'obj') {
            return;
        }
        $this->data[$name] = $value;
    }
    public function __get($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
    }

    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    public function __unset($name)
    {
        unset($this->data[$name]);
    }
}