<?php

namespace AdventureGame\Event;

abstract class InfiniteUseTrigger extends AbstractTrigger implements TriggerInterface
{
    protected int $triggerCount = 0;
}