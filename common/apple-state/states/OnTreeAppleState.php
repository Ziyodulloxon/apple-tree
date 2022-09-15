<?php

namespace common\appleState\states;

use common\appleState\AbstractAppleState;

class OnTreeAppleState extends AbstractAppleState
{
    public function fall(): FallenAppleState
    {
        return new FallenAppleState();
    }
}
