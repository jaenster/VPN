<?php


namespace VPN\Kernel;
/**
 * Class statusItem
 * @property $started
 */
use VPN\Lib\Collection\Item;

class StatusItem extends Item
{

    public function __construct($obj)
    {
        parent::__construct($obj);
        $this->started = false;

    }
}