<?php

namespace common\appleState;

class IllegalStateTransitionException extends \Exception
{
    public $message = "Illegal state transition";
}