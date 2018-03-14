<?php

// Try to load our extension if it's not already loaded.
if (!extension_loaded('SimplePcap')) {
    if (strtolower(substr(PHP_OS, 0, 3)) === 'win') {
        if (!dl('php_SimplePcap.dll')) return;
    } else {
        // PHP_SHLIB_SUFFIX gives 'dylib' on MacOS X but modules are 'so'.
        if (PHP_SHLIB_SUFFIX === 'dylib') {
            if (!dl('SimplePcap.so')) return;
        } else {
            if (!dl('SimplePcap.'.PHP_SHLIB_SUFFIX)) return;
        }
    }
}
