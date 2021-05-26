<?php

namespace AdventureGame\Event\Triggers;

use AdventureGame\Entity\ActivatableEntityInterface;
use AdventureGame\Traits\ActivatableTrait;

class ItemComparison implements ActivatableEntityInterface
{
    use ActivatableTrait;

    public function __construct(bool $activated)
    {
        $this->setActivated($activated);
    }
}