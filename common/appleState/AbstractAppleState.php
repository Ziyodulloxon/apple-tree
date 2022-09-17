<?php

namespace common\appleState;

use common\models\Apple;

abstract class AbstractAppleState implements AppleStateInterface
{
    /**
     * @throws IllegalStateTransitionException
     */
    public function fall()
    {
        throw new IllegalStateTransitionException();
    }

    /**
     * @throws IllegalStateTransitionException
     */
    public function eat()
    {
        throw new IllegalStateTransitionException();
    }

    /**
     * @throws IllegalStateTransitionException
     */
    public function biteOff(int $percent, Apple $apple)
    {
        throw new IllegalStateTransitionException();
    }

    /**
     * @throws IllegalStateTransitionException
     */
    public function rot(Apple $apple)
    {
        throw new IllegalStateTransitionException();
    }

    public function canRot(Apple $apple): bool
    {
        return time() - $apple->fallen_date >= 5 * 60 * 60;
    }

    public function canBiteOff(int $percent, Apple $apple): bool
    {
        return $apple->remained * 100 < $percent;
    }
}
