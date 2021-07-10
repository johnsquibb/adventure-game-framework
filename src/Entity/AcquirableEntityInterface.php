<?php

namespace AdventureGame\Entity;

/**
 * Interface AcquirableEntityInterface defines methods for items that are acquirable.
 * @package AdventureGame\Entity
 */
interface AcquirableEntityInterface
{
    public function getAcquirable(): bool;

    public function setAcquirable(bool $acquirable): void;
}
