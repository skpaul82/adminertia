<?php

namespace App\Actions;

abstract class Action
{
    /**
     * Execute the action.
     */
    abstract public function execute(...$parameters);
} 