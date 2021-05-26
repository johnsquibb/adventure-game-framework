<?php

namespace AdventureGame\Event;

abstract class InfiniteUseTrigger implements TriggerInterface
{
    protected int $triggerCount = 0;
}