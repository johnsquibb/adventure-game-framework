<?php

namespace AdventureGame\Entity;

/**
 * Interface InteractionEntityInterface defines methods for entities that can be interacted with.
 * @package AdventureGame\Entity
 */
interface InteractionEntityInterface extends EntityInterface
{
    public function getTag(): string;

    public function getAccessible(): bool;
}