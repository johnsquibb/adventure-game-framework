<?php

namespace AdventureGame\Command\Commands;

use AdventureGame\Game\Exception\InvalidExitException;
use AdventureGame\Game\Exception\PlayerLocationNotSetException;
use AdventureGame\Game\GameController;
use AdventureGame\IO\OutputController;
use AdventureGame\Item\ContainerInterface;
use AdventureGame\Item\ContainerItem;
use AdventureGame\Item\ItemInterface;
use AdventureGame\Location\Location;
use AdventureGame\Location\Portal;

/**
 * Class AbstractCommand provides common methods used by other Commands.
 * @package AdventureGame\Command\Commands
 */
abstract class AbstractCommand
{
    public function __construct(
        protected OutputController $outputController,
    ) {
    }

    /**
     * Add an item to player inventory.
     * @param GameController $gameController
     * @param ItemInterface $item
     */
    protected function addItemToPlayerInventory(
        GameController $gameController,
        ItemInterface $item
    ): void {
        $gameController->playerController->addItemToPlayerInventory($item);
        $this->outputController->addLine("Added {$item->getName()} to inventory");
    }

    /**
     * Describe an exit.
     * @param Portal $exit
     */
    protected function describeExit(Portal $exit): void
    {
        $this->outputController->addLines(
            [
                $exit->getName(),
                $exit->getDescription(),
            ]
        );
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
     * @param ItemInterface $item
     * @return void
     */
    protected function describeItem(ItemInterface $item): void
    {
        $this->outputController->addLines(
            [
                $item->getName(),
                $item->getDescription(),
            ]
        );
    }

    /**
     * Describe items at the current player location.
     * @param GameController $gameController
     * @throws PlayerLocationNotSetException
     */
    protected function describePlayerLocationItems(GameController $gameController): void
    {
        $this->describeLocationItems($gameController->mapController->getPlayerLocation());
    }

    /**
     * Describe items at Location.
     * @param Location $location
     * @return void
     */
    protected function describeLocationItems(Location $location): void
    {
        $items = $location->items->getItems();
        if (count($items) === 0) {
            return;
        }

        $this->outputController->addLines(
            [
                'You see the following items:',
            ]
        );

        foreach ($items as $item) {
            $this->describeItem($item);
        }
    }

    /**
     * Describe a list of items inside a container.
     * @param ContainerItem $container
     */
    protected function listContainerItems(ContainerInterface $container): void
    {
        $this->outputController->addLines(
            ["You see the following items inside " . $container->getName() . ": "],
        );

        foreach ($container->getItems() as $item) {
            if ($item instanceof ItemInterface) {
                $item->setAccessible(true);
                $this->listItem($item);
            }
        }
    }

    /**
     * List an item's name.
     * @param ItemInterface $item
     * @return void
     */
    protected function listItem(ItemInterface $item): void
    {
        $this->outputController->addLines(
            [
                $item->getName(),
            ]
        );
    }

    /**
     * Move player, describe the new location.
     * @param GameController $gameController
     * @param string $direction
     * @throws InvalidExitException
     * @throws PlayerLocationNotSetException
     */
    protected function movePlayer(GameController $gameController, string $direction): void
    {
        $gameController->mapController->movePlayer($direction);
        $this->describePlayerLocation($gameController);
    }

    /**
     * Describe the current player location.
     * @param GameController $gameController
     * @throws PlayerLocationNotSetException
     */
    protected function describePlayerLocation(GameController $gameController): void
    {
        $location = $gameController->mapController->getPlayerLocation();
        $this->describeLocation($location);
        $this->listLocationExits($location);
    }

    /**
     * Describe a location.
     * @param Location $location
     * @return void
     */
    protected function describeLocation(Location $location): void
    {
        $this->outputController->addLines(
            [
                $location->getName(),
                $location->getDescription(),
            ]
        );
    }

    /**
     * List exits for a location.
     * @param Location $location
     */
    protected function listLocationExits(Location $location): void
    {
        $this->outputController->addLines(
            [
                'You see the following exits: ',
            ]
        );

        foreach ($location->exits as $exit) {
            $this->listExit($exit);
        }
    }

    /**
     * List an exit.
     * @param Portal $exit
     */
    protected function listExit(Portal $exit): void
    {
        $this->outputController->addLines(
            [
                $exit->getName()
            ]
        );
    }

    /**
     * Remove an item from player inventory.
     * @param GameController $gameController
     * @param ItemInterface $item
     */
    protected function removeItemFromPlayerInventory(
        GameController $gameController,
        ItemInterface $item
    ): void {
        $gameController->playerController->removeItemFromPlayerInventory($item);
        $this->outputController->addLine("Removed {$item->getName()} from inventory");
    }
}