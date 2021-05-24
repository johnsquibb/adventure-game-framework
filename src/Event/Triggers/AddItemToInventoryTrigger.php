<?php

namespace AdventureGame\Event\Triggers;

use AdventureGame\Game\GameController;
use AdventureGame\Response\Response;

class AddItemToInventoryTrigger extends AddItemTrigger
{
    /**
     * Add item to player inventory.
     * @param GameController $gameController
     * @return Response|null
     */
    public function execute(GameController $gameController): ?Response
    {
        if ($this->triggerCount < $this->numberOfUses) {
            $gameController->playerController->addItemToPlayerInventory($this->item);
            $this->triggerCount++;

            $response = new Response();
            $response->addMessage("Added \"{$this->item->getName()}\" to inventory.");
            return $response;
        }

        return null;
    }
}