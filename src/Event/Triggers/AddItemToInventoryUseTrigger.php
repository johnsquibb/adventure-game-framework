<?php

namespace AdventureGame\Event\Triggers;

use AdventureGame\Event\FiniteUseTrigger;
use AdventureGame\Game\GameController;
use AdventureGame\Item\Item;
use AdventureGame\Item\ItemInterface;
use AdventureGame\Response\Response;

class AddItemToInventoryUseTrigger extends FiniteUseTrigger
{
    public function __construct(protected ItemInterface $item, int $numberOfUses = 1)
    {
        $this->numberOfUses = $numberOfUses;
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