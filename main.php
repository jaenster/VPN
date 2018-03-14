<?php

// The file that starts it all

require_once 'RawSocket/include.php';

require_once 'Collection/include.php';

require_once 'Kernel/include.php';

require_once 'Socket/include.php';

require_once 'Daemon/include.php';

require_once 'Configuration/include.php';


\Kernel\Kernel::start();