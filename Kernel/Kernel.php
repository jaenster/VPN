<?php
namespace Kernel;


use Collection\Collection;

class Kernel
{
    static private $processes = null;

    public static function register(Runnable $obj) : void
    {
        if (self::$processes === null){
            self::$processes = new Collection(StatusItem::class);
        }
        self::$processes->attach($obj);
    }
    public static function detach(Runnable $obj) : void
    {
        $processes = self::$processes;
        if (!$processes instanceof Collection)
        {
            return;
        }
        $processes->detach($obj);
        return;
    }
    public static function start() : void{

        if (!self::breakLoopCheck())
        {
            foreach(self::$processes as $item) {
                if (!$item->obj instanceof Runnable) {
                    continue;
                }
                $item->obj->start();
            }
        }

        // Loop for ever
        while (!self::breakLoopCheck()) {

            // Loop trough processes
            foreach (self::$processes as $item) {
                if (!$item->obj instanceof Runnable){
                    continue;
                }
                // Give a process some time
                if (!$item->started){
                    $item->started = true;
                    $item->obj->start();
                }

                $item->obj->run();
            }

            // sleep for a microsecond avoid full cpu usage
            usleep(1);
        }
    }

    static private function breakLoopCheck() : bool
    {
        $processes = self::$processes;
        if (!$processes instanceof Collection)
        {
            return true;
        }
        if ($processes->count() === 0 )
        {
            return true;
        }
        return false;
    }
    static public function getObjectByClassName(string $className) : object
    {
        // Loop trough objects
        foreach (self::$processes as $obj) {

            // Is $obj instanceof $className
            if ($obj instanceof $className){

                // Return $obj
                return $obj;
            }
        }
        throw new \Exception($className.' no such class');
    }

    static public function callMethod(string $methodName,$args=[]) : void{
        foreach (self::$processes as $obj) {
            if (method_exists($obj,$methodName)) {
                call_user_func_array(array($obj, $methodName), $args);
            }
        }
    }

}