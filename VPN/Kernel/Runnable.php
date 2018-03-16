<?php

namespace VPN\Kernel;


interface Runnable
{
    public function start() : void;
    public function run() : void;
}