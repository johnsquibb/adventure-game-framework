<?php

namespace AdventureGame\Command\Commands;

use AdventureGame\Entity\EntityInterface;
use AdventureGame\Entity\LockableInterface;
use AdventureGame\Game\Exception\ExitIsLockedException;
use AdventureGame\Game\Exception\InvalidExitException;
use AdventureGame\Game\Exception\PlayerLocationNotSetException;
use AdventureGame\Game\GameController;
use AdventureGame\Item\ContainerInterface;
use AdventureGame\Item\ContainerItem;
use AdventureGame\Item\ItemInterface;
use AdventureGame\Location\Location;
use AdventureGame\Location\Portal;
use AdventureGame\Response\Description;
use AdventureGame\Response\Response;

/**
 * Class AbstractCommand provides common methods used by other Commands.
 * @package AdventureGame\Command\Commands
 */
abstract class AbstractCommand
{
    /**
     * Add an item to player inventory.
     * @param GameController $gameController
     * @param ItemInterface $item
     * @return string
     */
    protected function addItemToPlayerInventory(
        GameController $gameController,
        ItemInterface $item
    ): string {
        $gameController->playerController->addItemToPlayerInventory($item);
        $gameController->eventController->processInventoryTakeEvents(
            $gameController,
            $item->getId()
        );

        return "Added {$item->getName()} to inventory";
    }

    /**
     * Describe a list of items.
     * @param array $items
     * @return array
     */
    protected function describeItems(array $items): array
    {
        $descriptions = [];

        foreach ($items as $item) {
            $descriptions[] = $this->describeItem($item);
        }

        return $descriptions;
    }

    /**
     * Describe an item.
     * @param ItemInterface $item
     * @return Description
     */
    protected function describeItem(ItemInterface $item): Description
    {
        return new Description($item->getName(), $item->getSummary(), $item->getDescription());
    }

    /**
     * Describe items at Location.
     * @param Location $location
     * @return array
     */
    protected function describeLocationItems(Location $location): array
    {
        $descriptions = [];

        $items = $location->getContainer()->getItems();
        foreach ($items as $item) {
            $descriptions[] = $this->describeItem($item);
        }

        return $descriptions;
    }

    /**
     * Describe a list of items inside a container.
     * @param ContainerItem $container
     * @return array
     */
    protected function listContainerItems(ContainerInterface $container): array
    {
        $descriptions = [];

        foreach ($container->getItems() as $item) {
            if ($item instanceof ItemInterface) {
                $item->setAccessible(true);
                $descriptions[] = $this->listItem($item);
            }
        }

        return $descriptions;
    }

    /**
     * List an item's name.
     * @param ItemInterface $item
     * @return Description
     */
    protected function listItem(ItemInterface $item): Description
    {
        return new Description($item->getName(), $item->getSummary(), $item->getDescription());
    }

    /**
     * List items for a location.
     * @param Location $location
     * @return array
     */
    protected function listLocationItems(Location $location): array
    {
        $descriptions = [];

        foreach ($location->getContainer()->getItems() as $item) {
            $descriptions[] = $this->listItem($item);
        }

        return $descriptions;
    }

    /**
     * Lock entity with key.
     * @param EntityInterface $entity
     * @param ItemInterface $key
     * @return string
     */
    protected function lockEntityWithKey(EntityInterface $entity, ItemInterface $key): string
    {
        if (is_a($entity, LockableInterface::class)) {
            $entity->setLocked(true);

            return "Locked {$entity->getName()} with {$key->getName()}.";
        }

        return "Can't lock that.";
    }

    /**
     * Move player, describe the new location.
     * @param GameController $gameController
     * @param string $direction
     * @return Response|null
     * @throws InvalidExitException
     * @throws PlayerLocationNotSetException
     */
    protected function movePlayer(GameController $gameController, string $direction): ?Response
    {
        try {
            $gameController->mapController->movePlayer($direction);
            return $this->describePlayerLocation($gameController);
        } catch (ExitIsLockedException $e) {
            $portal = $gameController->mapController
                ->getPlayerLocation()
                ->getExitInDirection($direction);

            $response = new Response();

            $response->addMessage("{$portal->getName()} is locked!");

            return $response;
        }
    }

    /**
     * Describe the current player location.
     * @param GameController $gameController
     * @return Response
     * @throws PlayerLocationNotSetException
     */
    protected function describePlayerLocation(GameController $gameController): Response
    {
        $response = new Response();

        $location = $gameController->mapController->getPlayerLocation();

        $description = $this->describeLocation($location);
        $response->addLocationDescription($description);

        foreach ($this->listLocationExits($location) as $description) {
            $response->addExitDescription($description);
        }

        foreach ($this->listLocationItems($location) as $description) {
            $response->addItemDescription($description);
        }

        return $response;
    }

    /**
     * Describe a location.
     * @param Location $location
     * @return Description
     */
    protected function describeLocation(Location $location): Description
    {
        return new Description(
            $location->getName(),
            $location->getSummary(),
            $location->getDescription()
        );
    }

    /**
     * List exits for a location.
     * @param Location $location
     * @return array
     */
    protected function listLocationExits(Location $location): array
    {
        $descriptions = [];

        foreach ($location->getExits() as $exit) {
            $descriptions[] = $this->listExit($exit);
        }

        return $descriptions;
    }

    /**
     * List an exit.
     * @param Portal $exit
     * @return Description
     */
    protected function listExit(Portal $exit): Description
    {
        return new Description($exit->getName(), $exit->getSummary(), $exit->getDescription());
    }

    /**
     * Remove an item from player inventory.
     * @param GameController $gameController
     * @param ItemInterface $item
     * @return string response message
     */
    protected function removeItemFromPlayerInventory(
        GameController $gameController,
        ItemInterface $item
    ): string {
        $gameController->playerController->removeItemFromPlayerInventory($item);

        return "Removed {$item->getName()} from inventory";
    }

    /**
     * @param EntityInterface $entity
     * @param ItemInterface $key
     * @return string response message
     */
    protected function unlockEntityWithKey(EntityInterface $entity, ItemInterface $key): string
    {
        if (is_a($entity, LockableInterface::class)) {
            $entity->setLocked(false);

            return "Unlocked {$entity->getName()} with {$key->getName()}.";
        }
    }
}