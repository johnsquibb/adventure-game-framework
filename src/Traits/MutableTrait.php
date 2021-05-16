<?php

namespace AdventureGame\Traits;

trait MutableTrait
{
    private bool $mutable = false;

    public function getMutable(): bool
    {
        return $this->mutable;
    }

    public function setMutable(bool $mutable): void
    {
        $this->mutable = $mutable;
    }
}