<?php

namespace VPN\Lib\Collection;
use ReflectionClass;

class Collection extends \SplObjectStorage
{
    protected  $itemClass;
    public function __construct(string $className)
    {
        $this->itemClass = $className;
        if (!$this->isItemAParent()) {
            throw new \Exception($className.' doesn\'t extend '.Item::class);
        }


    }
    public function attach($obj,$data = null) : void
    {
        $item = $this->createItem($obj);
        parent::attach($item,$data);

    }
    public function detach($obj) : void
    {
        if (!$obj instanceof $this->itemClass){
            return;
        }
        parent::detach($obj);

    }
    private function isItemAParent() : bool{
        $class = new ReflectionClass($this->itemClass);

        while ($parent = $class->getParentClass()) {
            if ($parent->getName() === Item::class)
            {
                return true;
            }
        }
        return false;
    }
    private function createItem($obj) : Item
    {
        return new $this->itemClass($obj);
    }

}