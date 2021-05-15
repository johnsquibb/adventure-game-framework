<?php

namespace AdventureGame\Entity;

/**
 * Interface LockableInterface defines methods for entities that are lockable.
 * @package AdventureGame\Entity
 */
interface LockableInterface
{
    public function getLocked(): bool;

    public function setLocked(bool $locked): void;
}