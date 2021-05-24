<?php

namespace AdventureGame\Traits;

trait ActivatableTrait
{
    private bool $activatable = true;
    private bool $activated = false;

    public function getActivatable(): bool
    {
        return $this->activatable;
    }

    public function setActivated(bool $activated): void
    {
        $this->activated = $activated;
    }

    public function getActivated(): bool
    {
        return $this->activated;
    }

    public function setActivatable(bool $activatable): void
    {
        $this->activatable = $activatable;
    }
}