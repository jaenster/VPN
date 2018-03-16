<?php

// The file that starts it all

require_once 'RawSocket/include.php';

require_once 'Collection/include.php';

require_once 'Kernel/include.php';


require_once 'VPN/include.php';

require_once 'Configuration/include.php';



// Load the config
\Configuration\Conf::init($argv[1]);


// Transport layer
\Configuration\Conf::$transport = new \VPN\Transfer\Transport();

//
\Kernel\Kernel::start();