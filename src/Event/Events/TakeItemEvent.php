<?php

namespace AdventureGame\Event\Events;

use AdventureGame\Event\AbstractInventoryEvent;
use AdventureGame\Event\Triggers\AddItemToInventoryTrigger;
use AdventureGame\Game\GameController;

class TakeItemEvent extends AbstractInventoryEvent
{
    public function trigger(GameController $gameController): void
    {
        $this->trigger->execute($gameController);
    }
}