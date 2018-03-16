<?php

namespace Rawsocket\Layer;


interface Layer2 extends LayerGeneral
{
    public function getNextLayer(): Layer3;
}