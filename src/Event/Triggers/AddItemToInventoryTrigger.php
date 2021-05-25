<?php

namespace AdventureGame\Event\Triggers;

use AdventureGame\Event\AbstractTrigger;
use AdventureGame\Game\GameController;
use AdventureGame\Item\Item;
use AdventureGame\Response\Response;

class AddItemToInventoryTrigger extends AbstractTrigger
{
    public function __construct(protected Item $item, protected int $numberOfUses = 1)
    {
    }

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