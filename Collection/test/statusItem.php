<?php

/**
 * Class statusItem
 * @property $started
 */
class statusItem extends \Collection\Item
{
    public function __construct($obj)
    {
        parent::__construct($obj);
        $this->started = false;

    }
}