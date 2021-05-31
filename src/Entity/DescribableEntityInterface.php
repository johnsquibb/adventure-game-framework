<?php

namespace AdventureGame\Entity;

/**
 * Interface DescribableEntityInterface defines methods for describable entities.
 * @package AdventureGame\Entity
 */
interface DescribableEntityInterface
{
    public function getDescription(): string;

    public function getSummary(): string;
}