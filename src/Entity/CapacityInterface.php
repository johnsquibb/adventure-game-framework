<?php

namespace AdventureGame\Entity;

/**
 * Interface CapacityInterface defines methods for entities that have a capacity.
 * @package AdventureGame\Entity
 */
interface CapacityInterface
{
    public function getCapacity(): int;

    public function setCapacity(int $capacity);
}