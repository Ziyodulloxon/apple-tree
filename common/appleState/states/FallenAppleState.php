<?php

namespace common\appleState\states;

use common\appleState\AbstractAppleState;
use common\appleState\IllegalStateTransitionException;
use common\models\Apple;

class FallenAppleState extends AbstractAppleState
{
    /**
     * @throws IllegalStateTransitionException
     */
    public function rot(Apple $apple): RottenAppleState
    {
        if (!$this->canRot($apple)) {
            throw new IllegalStateTransitionException("This apple too fresh to be rotten");
        }
        return new RottenAppleState();
    }

    public function biteOff(int $percent, Apple $apple): self
    {
        if ($this->canBiteOff($percent, $apple)) {
            throw new IllegalStateTransitionException("There's less apple left than you're going to bite into");
        }
        return $this;
    }

    public function eat(): BittenOffAppleState
    {
        return new BittenOffAppleState();
    }
}
