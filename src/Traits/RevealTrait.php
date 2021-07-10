<?php

namespace AdventureGame\Traits;

/**
 * Trait RevealTrait provides methods for objects implementing RevealInterface.
 * @package AdventureGame\Traits
 */
trait RevealTrait
{
    /**
     * @var bool Whether the items in the container have been revealed.
     */
    private bool $revealed = false;

    /**
     * Get revealed state.
     * @return bool
     */
    public function getRevealed(): bool
    {
        return $this->revealed;
    }

    /**
     * Set revealed state.
     * @param bool $revealed
     */
    public function setRevealed(bool $revealed): void
    {
        $this->revealed = $revealed;
    }
}
