<?php

namespace AdventureGame\Traits;

trait LockableTrait
{
    private bool $locked = false;

    public function getLocked(): bool
    {
        return $this->locked;
    }

    public function setLocked(bool $locked): void
    {
        $this->locked = $locked;
    }
}