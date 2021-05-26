<?php

namespace AdventureGame\Event;

abstract class FiniteUseTrigger implements TriggerInterface
{
    protected int $triggerCount = 0;
    protected int $numberOfUses = 1;
}