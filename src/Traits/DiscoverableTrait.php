<?php

namespace AdventureGame\Traits;

trait DiscoverableTrait
{
    private bool $discovered = false;

    public function getDiscovered(): bool
    {
        return $this->discovered;
    }

    public function setDiscovered(bool $discovered): void
    {
        $this->discovered = $discovered;
    }
}