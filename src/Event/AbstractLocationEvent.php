<?php

namespace AdventureGame\Event;

abstract class AbstractLocationEvent extends AbstractEvent
{
    public function __construct(
        TriggerInterface $trigger,
        string $matchLocationId = '',
    ) {
        $this->trigger = $trigger;
        $this->matchLocationId = $matchLocationId;
    }
}