<?php

namespace AdventureGame\Entity;

/**
 * Interface RevealableEntityInterface defines methods for entities that can be revealed.
 * @package AdventureGame\Entity
 */
interface RevealableEntityInterface
{
    public function getRevealed(): bool;

    public function setRevealed(bool $revealed);
}