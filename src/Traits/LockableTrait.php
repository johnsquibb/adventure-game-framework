<?php

namespace AdventureGame\Traits;

trait LockableTrait
{
    use MutableTrait;

    private bool $locked = false;
    private string $keyEntityId = '';

    public function getLocked(): bool
    {
        return $this->locked;
    }

    public function setLocked(bool $locked): void
    {
        if ($this->mutable) {
            $this->locked = $locked;
        }
    }

    public function getKeyEntityId(): string
    {
        return $this->keyEntityId;
    }

    public function setKeyEntityId(string $keyEntityId): void
    {
        $this->keyEntityId = $keyEntityId;
    }

    public function getMutable(): bool
    {
        return $this->mutable;
    }

    public function setMutable(bool $mutable): void
    {
        $this->mutable = $mutable;
    }
}