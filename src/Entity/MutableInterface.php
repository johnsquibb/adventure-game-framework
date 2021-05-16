<?php

namespace AdventureGame\Entity;

/**
 * Interface MutableInterface defines methods for entities that are mutable.
 * @package AdventureGame\Entity
 */
interface MutableInterface
{
    public function getMutable(): bool;

    public function setMutable(bool $lockable): void;
}