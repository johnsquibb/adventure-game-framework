<?php

namespace AdventureGame\Entity;

/**
 * Interface RevealInterface defines methods for entities that can be revealed.
 * @package AdventureGame\Entity
 */
interface RevealInterface
{
    public function getRevealed(): bool;

    public function setRevealed(bool $revealed);
}