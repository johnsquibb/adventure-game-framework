<?php

namespace AdventureGame\Entity;

/**
 * Interface AcquirableEntityInterface defines methods for items that are acquirable.
 * @package AdventureGame\Entity
 */
interface AcquirableEntityInterface extends TaggableEntityInterface
{
    public function getAcquirable(): bool;

    public function setAcquirable(bool $acquirable): void;
}