<?php

namespace AdventureGame\Traits;

trait LockableTrait
{
    private bool $locked = false;
    private string $keyEntityId = '';

    public function getLocked(): bool
    {
        return $this->locked;
    }

    public function setLocked(bool $locked): void
    {
        $this->locked = $locked;
    }

    public function getKeyEntityId(): string
    {
        return $this->keyEntityId;
    }

    public function setKeyEntityId(string $keyEntityId): void
    {
        $this->keyEntityId = $keyEntityId;
    }
}