<?php

namespace AdventureGame\Event\Events;

use AdventureGame\Event\AbstractInventoryEvent;
use AdventureGame\Game\GameController;
use AdventureGame\Response\Response;

class DropItemEvent extends AbstractInventoryEvent
{
    /**
     * Trigger the drop item event.
     * @param GameController $gameController
     * @return Response|null
     */
    public function trigger(GameController $gameController): ?Response
    {
        return $this->trigger->execute($gameController);
    }
}