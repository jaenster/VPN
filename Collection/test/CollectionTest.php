<?php


require_once '../Collection.php';
require_once '../Item.php';
require_once 'statusItem.php';

class Foo {
    public $bar = 'data';
}

$collection = new \Collection\Collection(statusItem::class);

$collection->attach(new Foo);

foreach ($collection as $item)
{
    var_dump($item->started);
    var_dump($item->obj);
}

/*
bool(false)
object(Foo)#3 (1) {
  ["bar"]=>
  string(4) "data"
}
 */



