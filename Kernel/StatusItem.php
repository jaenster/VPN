<?php


namespace Kernel;
/**
 * Class statusItem
 * @property $started
 */
use Collection\Item;

class StatusItem extends Item
{

    public function __construct($obj)
    {
        parent::__construct($obj);
        $this->started = false;

    }
}