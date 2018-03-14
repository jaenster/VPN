<?php



require_once '../include.php';


use Rawsocket\Route\Routes;

print (new Routes())
        ->AddSystemRoutes()
        ->getRoute((new \Rawsocket\Model\IPv4Address($argv[1])))
    . PHP_EOL;







