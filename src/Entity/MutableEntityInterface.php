<?php

namespace AdventureGame\Entity;

/**
 * Interface MutableEntityInterface defines methods for entities that are mutable.
 * @package AdventureGame\Entity
 */
interface MutableEntityInterface
{
    public function getMutable(): bool;

    public function setMutable(bool $lockable): void;
}
