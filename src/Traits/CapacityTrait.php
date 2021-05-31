<?php

namespace AdventureGame\Traits;

/**
 * Trait CapacityTrait provides methods for objects implementing CapacityInterface
 * @package AdventureGame\Traits
 */
trait CapacityTrait
{
    /**
     * Capacity is an arbitrary estimation of weight+size.
     * Bigger items and heavier items require more capacity.
     * @var int
     */
    private int $capacity = 0;

    /**
     * Return capacity.
     * @return int
     */
    public function getCapacity(): int
    {
        return $this->capacity;
    }

    /**
     * Set capacity.
     * @param int $capacity
     */
    public function setCapacity(int $capacity): void
    {
        $this->capacity = $capacity;
    }
}