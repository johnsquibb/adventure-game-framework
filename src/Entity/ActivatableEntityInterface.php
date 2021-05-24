<?php

namespace AdventureGame\Entity;

/**
 * Interface ActivatableEntityInterface defines methods for items that can be activated.
 * @package AdventureGame\Entity
 */
interface ActivatableEntityInterface
{
    public function getActivatable(): bool;

    public function setActivated(bool $activated): void;

    public function getActivated(): bool;

    public function setActivatable(bool $activatable): void;
}