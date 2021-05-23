<?php

namespace AdventureGame\Event\Triggers;

use AdventureGame\Event\AbstractTrigger;
use AdventureGame\Game\GameController;
use AdventureGame\Item\ItemInterface;
use AdventureGame\Response\Response;

class DropItemFromInventoryTrigger extends AbstractTrigger
{
    public function __construct(private string $itemId)
    {
    }

    /**
     * Add item to player inventory.
     * @param GameController $gameController
     * @return Response|null
     */
    public function execute(GameController $gameController): ?Response
    {
        $item = $gameController->playerController->getItemByIdFromPlayerInventory($this->itemId);

        if ($item instanceof ItemInterface) {
            $gameController->playerController->removeItemFromPlayerInventory($item);
            $gameController->mapController->getPlayerLocation()->getContainer()->addItem($item);

            $response = new Response();
            $response->addMessage("Removed \"{$item->getName()}\" from inventory.");

            return $response;
        }

        return null;
    }
}