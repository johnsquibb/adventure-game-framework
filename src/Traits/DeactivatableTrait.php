<?php

namespace AdventureGame\Traits;

trait DeactivatableTrait
{
    use ActivatableTrait;

    private bool $deactivatable = false;

    public function getDeactivatable(): bool
    {
        return $this->deactivatable;
    }

    public function setDeactivated(bool $deactivated): void
    {
        $this->activated = !$deactivated;
    }

    public function getDeactivated(): bool
    {
        return !$this->activated;
    }

    public function setDeactivatable(bool $deactivatable): void
    {
        $this->deactivatable = $deactivatable;
    }
}