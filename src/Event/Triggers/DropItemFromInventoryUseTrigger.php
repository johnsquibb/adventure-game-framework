<?php

namespace AdventureGame\Event\Triggers;

use AdventureGame\Event\FiniteUseTrigger;
use AdventureGame\Game\Exception\PlayerLocationNotSetException;
use AdventureGame\Game\GameController;
use AdventureGame\Item\ItemInterface;
use AdventureGame\Response\Response;

class DropItemFromInventoryUseTrigger extends FiniteUseTrigger
{
    public function __construct(private string $itemId, int $numberOfUses = 1)
    {
        $this->numberOfUses = $numberOfUses;
    }

    /**
     * Drop item from player inventory.
     * @param GameController $gameController
     * @return Response|null
     * @throws PlayerLocationNotSetException
     */
    public function execute(GameController $gameController): ?Response
    {
        $item = $gameController->playerController->getItemByIdFromPlayerInventory($this->itemId);

        if ($this->triggerCount < $this->numberOfUses) {
            if ($item instanceof ItemInterface) {
                $gameController->playerController->removeItemFromPlayerInventory($item);
                $gameController->mapController->getPlayerLocation()->getContainer()->addItem($item);
                $this->triggerCount++;

                $response = new Response();
                $response->addMessage("Removed \"{$item->getName()}\" from inventory.");

                return $response;
            }
        }

        return null;
    }
}