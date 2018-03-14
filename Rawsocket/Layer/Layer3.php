<?php

namespace Rawsocket\Layer;

interface Layer3 extends LayerGeneral
{
    public function getNextLayer(): Layer4;
}