<?php

namespace AdventureGame\Event;

abstract class AbstractInventoryEvent extends AbstractEvent
{
    public function __construct(
        TriggerInterface $trigger,
        string $matchItemId = '',
        string $matchLocationId = '',
    ) {
        $this->trigger = $trigger;
        $this->matchItemId = $matchItemId;
        $this->matchLocationId = $matchLocationId;
    }
}
