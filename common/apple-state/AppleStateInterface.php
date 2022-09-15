<?php

namespace common\appleState;

use common\models\Apple;

interface AppleStateInterface
{
    public function fall();

    public function eat();

    public function biteOff(int $percent, Apple $apple);

    public function rot(Apple $apple);
}
