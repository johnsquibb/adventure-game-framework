<?php

namespace AdventureGame\Event\Triggers;
use AdventureGame\Event\AbstractTrigger;
use AdventureGame\Item\Item;

abstract class AddItemTrigger extends AbstractTrigger
{
    protected int $triggerCount = 0;

    public function __construct(protected Item $item, protected int $numberOfUses = 1)
    {
    }
}