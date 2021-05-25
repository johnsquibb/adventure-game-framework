<?php

namespace AdventureGame\Entity;

/**
 * Interface DeactivatableEntityInterface defines methods for items that can be Deactivated.
 * @package AdventureGame\Entity
 */
interface DeactivatableEntityInterface
{
    public function getDeactivatable(): bool;

    public function setDeactivated(bool $Deactivated): void;

    public function getDeactivated(): bool;

    public function setDeactivatable(bool $Deactivatable): void;
}