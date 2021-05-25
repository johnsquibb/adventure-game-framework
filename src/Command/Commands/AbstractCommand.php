<?php

namespace AdventureGame\Command\Commands;

use AdventureGame\Entity\ActivatableEntityInterface;
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
use AdventureGame\Response\ItemDescription;
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
     * @return Response
     * @throws PlayerLocationNotSetException
     */
    protected function addItemToPlayerInventory(
        GameController $gameController,
        ItemInterface $item
    ): Response {
        $response = new Response();

        $gameController->getPlayerController()->addItemToPlayerInventory($item);
        $response->addMessage("Added \"{$item->getName()}\" to inventory.");

        $eventResponse = $gameController->getEventController()->processTakeItemEvents(
            $gameController,
            $item->getId()
        );

        if ($eventResponse) {
            $response->addMessages($eventResponse->getMessages());
        }

        return $response;
    }

    /**
     * Remove an item from player inventory.
     * @param GameController $gameController
     * @param ItemInterface $item
     * @return Response response message
     * @throws PlayerLocationNotSetException
     */
    protected function removeItemFromPlayerInventory(
        GameController $gameController,
        ItemInterface $item
    ): Response {
        $response = new Response();

        $gameController->getPlayerController()->removeItemFromPlayerInventory($item);
        $response->addMessage("Removed \"{$item->getName()}\" from inventory");

        $eventResponse = $gameController->getEventController()->processDropItemEvents(
            $gameController,
            $item->getId()
        );

        if ($eventResponse) {
            $response->addMessages($eventResponse->getMessages());
        }

        return $response;
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
        return new ItemDescription(
            $item->getName(),
            $item->getSummary(),
            $item->getDescription(),
            $item->getTags()
        );
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
                // Now that it's been discovered, it can be taken.
                $item->setAccessible(true);
                $descriptions[] = $this->listItem($item);
            }
        }

        return $descriptions;
    }

    /**
     * List an item's name.
     * @param ItemInterface $item
     * @return ItemDescription
     */
    protected function listItem(ItemInterface $item): ItemDescription
    {
        $description = new ItemDescription(
            $item->getName(),
            $item->getSummary(),
            $item->getDescription(),
            $item->getTags()
        );

        if ($item instanceof ActivatableEntityInterface) {
            if ($item->getActivated()) {
                $description->setStatus('activated');
            }
        }

        return $description;
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
    protected function movePlayer(GameController $gameController, string $direction): Response
    {
        try {
            // Process leave current location events.
            $exitLocationEventResponse = $gameController->getEventController()->processExitLocationEvents(
                $gameController,
                $gameController->getMapController()->getPlayerLocation()->getId()
            );

            $gameController->getMapController()->movePlayer($direction);
            $response = $this->describePlayerLocation($gameController);

            // Process enter new location events.
            $enterLocationEventResponse = $gameController->getEventController()->processEnterLocationEvents(
                $gameController,
                $gameController->getMapController()->getPlayerLocation()->getId()
            );

            // Process item-specific events when entering new location.
            foreach ($gameController->getPlayerController()->getPlayerInventory()->getItems() as $item) {
                if ($item instanceof ItemInterface) {
                    if ($item->getActivated()) {
                        $hasActivatedItemEventResponse = $gameController->getEventController()->processHasActivatedItemEvents(
                            $gameController,
                            $item->getId()
                        );

                        if ($hasActivatedItemEventResponse) {
                            $response->addMessages($hasActivatedItemEventResponse->getMessages());
                        }
                    }
                }
            }

            if ($exitLocationEventResponse) {
                $response->addMessages($exitLocationEventResponse->getMessages());
            }

            if ($enterLocationEventResponse) {
                $response->addMessages($enterLocationEventResponse->getMessages());
            }

        } catch (ExitIsLockedException $e) {
            $portal = $gameController->getMapController()
                ->getPlayerLocation()
                ->getExitInDirection($direction);

            $response = new Response();
            $response->addMessage("{$portal->getName()} is locked!");
        }

        return $response;
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

        $location = $gameController->getMapController()->getPlayerLocation();

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
     * Describe the items in the player inventory.
     * @param GameController $gameController
     * @return Response
     */
    protected function describePlayerInventory(GameController $gameController): Response
    {
        $response = new Response();

        $inventory = $gameController->getPlayerController()->getPlayerInventory();

        foreach ($this->listContainerItems($inventory) as $description) {
            $response->addInventoryItemDescription($description);
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

        return "Can't unlock that";
    }

    /**
     * Try to look at items in the current player location.
     * @param GameController $gameController
     * @param string $tag
     * @return Response
     * @throws PlayerLocationNotSetException
     */
    protected function tryLookAtItemsByTagAtPlayerLocationAction(
        GameController $gameController,
        string $tag
    ): Response {
        $response = new Response();

        $items = $gameController->getMapController()
            ->getPlayerLocation()->getContainer()->getItemsByTag($tag);

        if (empty($items)) {
            $response->addMessage("You don't see anything like that here.");
        }

        foreach ($this->describeItems($items) as $description) {
            $response->addItemDescription($description);
        }

        return $response;
    }
}