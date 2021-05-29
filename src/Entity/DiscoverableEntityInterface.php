<?php

namespace AdventureGame\Entity;

/**
 * Interface AccessibleEntityInterface defines methods for entities that can be discovered.
 * @package AdventureGame\Entity
 */
interface DiscoverableEntityInterface extends TaggableEntityInterface
{
    public function getDiscovered(): bool;

    public function setDiscovered(bool $discovered): void;
}