<?php

namespace AdventureGame\Entity;

/**
 * Interface LockableInterface defines methods for entities that are lockable.
 * @package AdventureGame\Entity
 */
interface LockableInterface extends MutableInterface
{
    public function getKeyEntityId(): string;

    public function getLocked(): bool;

    public function getMutable(): bool;

    public function setKeyEntityId(string $keyEntityId): void;

    public function setLocked(bool $locked): void;

    public function setMutable(bool $lockable): void;
}