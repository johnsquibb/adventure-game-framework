<?php

namespace AdventureGame\Command\Commands;

use AdventureGame\Game\Exception\InvalidExitException;
use AdventureGame\Game\Exception\PlayerLocationNotSetException;
use AdventureGame\Game\GameController;
use AdventureGame\IO\OutputController;
use AdventureGame\Item\Item;
use AdventureGame\Location\Location;

abstract class AbstractCommand
{
    public function __construct(
        protected OutputController $outputController,
    ) {
    }

    /**
     * Move player, describe the new location.
     * @param GameController $gameController
     * @param $direction
     * @throws InvalidExitException
     * @throws PlayerLocationNotSetException
     */
    protected function movePlayer(GameController $gameController, string $direction): void
    {
        $gameController->mapController->movePlayer($direction);
        $this->describePlayerLocation($gameController);
    }

    /**
     * Add an item to player inventory.
     * @param GameController $gameController
     * @param Item $item
     */
    protected function addItemToPlayerInventory(GameController $gameController, Item $item): void
    {
        $gameController->playerController->addItemToPlayerInventory($item);
        $this->outputController->addLine("Added {$item->name} to inventory");
    }

    /**
     * Remove an item from player inventory.
     * @param GameController $gameController
     * @param Item $item
     */
    protected function removeItemFromPlayerInventory(
        GameController $gameController,
        Item $item
    ): void {
        $gameController->playerController->removeItemFromPlayerInventory($item);
        $this->outputController->addLine("Removed {$item->name} from inventory");
    }

    /**
     * Describe the current player location.
     * @param GameController $gameController
     * @throws PlayerLocationNotSetException
     */
    protected function describePlayerLocation(GameController $gameController): void
    {
        $lines = $this->describeLocation($gameController->mapController->getPlayerLocation());
        $this->outputController->addLines($lines);
    }

    /**
     * Describe a location.
     * @param Location $location
     * @return array
     */
    protected function describeLocation(Location $location): array
    {
        return [
            $location->name,
            $location->description,
        ];
    }

    /**
     * Describe a list of items.
     * @param array $items
     */
    protected function describeItems(array $items): void
    {
        foreach ($items as $item) {
            $this->describeItem($item);
        }
    }

    /**
     * Describe an item.
     * @param Item $item
     * @return void
     */
    protected function describeItem(Item $item): void
    {
        $this->outputController->addLines(
            [
                $item->name,
                $item->description,
            ]
        );
    }
}