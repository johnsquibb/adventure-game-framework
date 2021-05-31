<?php

namespace AdventureGame\Traits;

trait SizeTrait
{
    private int $size = 0;

    /**
     * Get the size.
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Set the size.
     * @param int $size
     */
    public function setSize(int $size): void
    {
        $this->size = $size;
    }
}