<?php

// The file that starts it all

require_once 'Lib/Collection/include.php';

require_once 'Lib/RawSocket/include.php';

require_once 'VPN/include.php';





// Load the config
VPN\Configuration\Conf::init($argv[1]);


// Transport layer
VPN\Configuration\Conf::$transport = new \VPN\Transfer\Transport();

//
VPN\Kernel\Kernel::start();