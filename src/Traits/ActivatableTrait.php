<?php

namespace AdventureGame\Traits;

trait ActivatableTrait
{
    private bool $activatable = false;
    private bool $activated = false;

    public function getActivatable(): bool
    {
        return $this->activatable;
    }

    public function setActivatable(bool $activatable): void
    {
        $this->activatable = $activatable;
    }

    public function getActivated(): bool
    {
        return $this->activated;
    }

    public function setActivated(bool $activated): void
    {
        $this->activated = $activated;
    }
}
