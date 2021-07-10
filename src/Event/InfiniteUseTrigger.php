<?php

namespace AdventureGame\Event;

abstract class InfiniteUseTrigger extends AbstractTrigger
{
    protected int $triggerCount = 0;
}
