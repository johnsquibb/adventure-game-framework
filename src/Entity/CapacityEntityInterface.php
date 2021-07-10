<?php

namespace AdventureGame\Entity;

/**
 * Interface CapacityEntityInterface defines methods for entities that have a capacity.
 * @package AdventureGame\Entity
 */
interface CapacityEntityInterface
{
    public function getCapacity(): int;

    public function setCapacity(int $capacity);

    public function hasCapacity(int $size): bool;
}
