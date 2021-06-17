<?php

namespace AdventureGame\Event;

abstract class FiniteUseTrigger extends AbstractTrigger
{
    protected int $triggerCount = 0;
    protected int $numberOfUses = 1;
}