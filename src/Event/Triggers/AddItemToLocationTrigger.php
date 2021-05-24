<?php

namespace AdventureGame\Event\Triggers;

use AdventureGame\Game\GameController;
use AdventureGame\Response\Response;

class AddItemToLocationTrigger extends AddItemTrigger
{
    /**
     * Add item to player location.
     * @param GameController $gameController
     * @return Response|null
     * @throws \AdventureGame\Game\Exception\PlayerLocationNotSetException
     */
    public function execute(GameController $gameController): ?Response
    {
        if ($this->triggerCount < $this->numberOfUses) {
            $gameController->mapController->addItem($this->item);
            $this->triggerCount++;

            $response = new Response();
            $response->addMessage("Revealed \"{$this->item->getName()}\".");
            return $response;
        }

        return null;
    }
}