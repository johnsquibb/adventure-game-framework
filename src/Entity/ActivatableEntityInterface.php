<?php

namespace AdventureGame\Entity;

/**
 * Interface ActivatableEntityInterface defines methods for items that can be activated.
 * @package AdventureGame\Entity
 */
interface ActivatableEntityInterface
{
    public const STATUS_ACTIVATED = 'activated';

    public function getActivatable(): bool;

    public function getActivated(): bool;

    public function setActivatable(bool $activatable): void;

    public function setActivated(bool $activated): void;
}