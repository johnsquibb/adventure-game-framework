<?php

namespace AdventureGame\Entity;

/**
 * Interface SizeInterface defines methods for entities that have a size.
 * Size is an arbitrary estimation of weight+dimensions to be used for Container capacity.
 * Larger, heavier objects should have a larger 'size'.
 * @package AdventureGame\Entity
 */
interface SizeInterface
{
    public function getSize(): int;

    public function setSize(int $size): void;
}