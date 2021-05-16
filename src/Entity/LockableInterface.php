<?php

namespace AdventureGame\Entity;

/**
 * Interface LockableInterface defines methods for entities that are lockable.
 * @package AdventureGame\Entity
 */
interface LockableInterface extends MutableInterface
{
    public function getMutable(): bool;

    public function setMutable(bool $lockable): void;

    public function getLocked(): bool;

    public function setLocked(bool $locked): void;

    public function getKeyEntityId(): string;

    public function setKeyEntityId(string $keyEntityId): void;
}