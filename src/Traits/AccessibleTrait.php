<?php

namespace AdventureGame\Traits;

trait AccessibleTrait
{
    private bool $accessible = true;

    public function getAccessible(): bool
    {
        return $this->accessible;
    }

    public function setAccessible(bool $accessible): void
    {
        $this->accessible = $accessible;
    }
}
