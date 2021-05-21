<?php

namespace AdventureGame\Event;

abstract class AbstractInventoryEvent implements EventInterface
{
    public function __construct(
        protected TriggerInterface $trigger,
        private string $matchItemId = '',
        private string $matchLocationId = '',
    ) {
    }

    public function matchItemId(string $itemId): bool
    {
        return $this->matchItemId === $itemId;
    }

    public function matchLocationId(string $locationId): bool
    {
        return $this->matchLocationId === $locationId;
    }
}