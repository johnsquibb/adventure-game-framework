<?php

namespace AdventureGame\Entity;

/**
 * Interface AccessibleEntityInterface defines methods for entities that can be interacted with.
 * @package AdventureGame\Entity
 */
interface AccessibleEntityInterface extends TaggableEntityInterface
{
    public function getAccessible(): bool;

    public function setAccessible(bool $accessible): void;
}