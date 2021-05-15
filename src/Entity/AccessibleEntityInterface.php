<?php

namespace AdventureGame\Entity;

/**
 * Interface AccessibleEntityInterface defines methods for entities that can be interacted with.
 * @package AdventureGame\Entity
 */
interface AccessibleEntityInterface extends EntityInterface
{
    public function getAccessible(): bool;

    public function getTag(): string;

    public function setAccessible(bool $accessible): void;
}