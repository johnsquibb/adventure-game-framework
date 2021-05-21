<?php

namespace AdventureGame\Event\Events;

use AdventureGame\Event\AbstractInventoryEvent;
use AdventureGame\Game\GameController;
use AdventureGame\Response\Response;

class TakeItemEvent extends AbstractInventoryEvent
{
    /**
     * Trigger the take item event.
     * @param GameController $gameController
     * @return Response|null
     */
    public function trigger(GameController $gameController): ?Response
    {
        return $this->trigger->execute($gameController);
    }
}