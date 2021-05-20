<?php

namespace AdventureGame\Traits;

trait AcquirableTrait
{
    private bool $acquirable = true;

    public function getAcquirable(): bool
    {
        return $this->acquirable;
    }

    public function setAcquirable(bool $acquirable): void
    {
        $this->acquirable = $acquirable;
    }
}