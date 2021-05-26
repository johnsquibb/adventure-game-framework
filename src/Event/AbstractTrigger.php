<?php

namespace AdventureGame\Event;

abstract class AbstractTrigger implements TriggerInterface
{
    protected int $triggerCount = 0;
    protected int $numberOfUses = 1;
}