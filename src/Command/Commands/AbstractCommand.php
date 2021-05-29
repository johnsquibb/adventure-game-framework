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
use AdventureGame\Response\Message\InventoryMessage;
use AdventureGame\Response\Message\ItemMessage;
use AdventureGame\Response\Message\LockableEntityMessage;
use AdventureGame\Response\Message\UnableMessage;
use AdventureGame\Response\Response;

/**
 * Class AbstractCommand provides common methods used by other Commands.
 * @package AdventureGame\Command\Commands
 */
abstract class AbstractCommand
{
    public const COMMAND_ACTIVATE = 'activate';
    public const COMMAND_DEACTIVATE = 'deactivate';
    public const COMMAND_DROP = 'drop';
    public const COMMAND_EXAMINE = 'examine';
    public const COMMAND_INVENTORY = 'inventory';
    public const COMMAND_LOAD = 'load';
    public const COMMAND_LOCK = 'lock';
    public const COMMAND_MOVE = 'move';
    public const COMMAND_NEW = 'new';
    public const COMMAND_PUT = 'put';
    public const COMMAND_QUIT = 'quit';
    public const COMMAND_READ = 'read';
    public const COMMAND_SAVE = 'save';
    public const COMMAND_TAKE = 'take';
    public const COMMAND_UNLOCK = 'unlock';

    /**
     * Describe the items in the player inventory.
     * @param GameController $gameController
     * @return Response
     */
    protected function describePlayerInventory(GameController $gameController): Response
    {
        $response = new Response();

        $inventory = $gameController->getPlayerController()->getPlayerInventory();

        if (empty($inventory->getItems())) {
            $message = new InventoryMessage(InventoryMessage::TYPE_INVENTORY_EMPTY);
            $response->addMessage($message->toString());
            return $response;
        }

        foreach ($this->listContainerItems($inventory) as $description) {
            $response->addInventoryItemDescription($description);
        }

        return $response;
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
                // Now that item has been discovered, it can be interacted with.
                $item->setDiscovered(true);
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
                $description->setStatus(ActivatableEntityInterface::STATUS_ACTIVATED);
            }
        }

        return $description;
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

            $message = new LockableEntityMessage($entity, $key, LockableEntityMessage::TYPE_LOCK);

            return $message->toString();
        }

        $message = new UnableMessage($entity->getName(), UnableMessage::TYPE_CANNOT_LOCK);
        return $message->toString();
    }

    /**
     * Move player, describe the new location.
     * @param GameController $gameController
     * @param string $direction
     * @return Response
     * @throws InvalidExitException
     * @throws PlayerLocationNotSetException
     */
    protected function movePlayer(GameController $gameController, string $direction): Response
    {
        try {
            // Process leave current location events.
            $exitLocationEventResponse = $gameController->getEventController(
            )->processExitLocationEvents(
                $gameController,
                $gameController->getMapController()->getPlayerLocation()->getId()
            );

            $gameController->getMapController()->movePlayer($direction);
            $response = $this->describePlayerLocation($gameController);

            // Process enter new location events.
            $enterLocationEventResponse = $gameController->getEventController(
            )->processEnterLocationEvents(
                $gameController,
                $gameController->getMapController()->getPlayerLocation()->getId()
            );

            // Process item-specific events when entering new location.
            foreach (
                $gameController->getPlayerController()->getPlayerInventory()->getItems() as $item
            ) {
                if ($item instanceof ItemInterface) {
                    if ($item->getActivated()) {
                        $hasActivatedItemEventResponse = $gameController->getEventController(
                        )->processHasActivatedItemEvents(
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
            $message = new UnableMessage($portal->getName(), UnableMessage::TYPE_PORTAL_LOCKED);
            $response->addMessage($message->toString());
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
        $itemMessage = new ItemMessage($item, ItemMessage::TYPE_REMOVE);
        $response->addMessage($itemMessage->toString());

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
     * Take all the items by tag from container matching another tag at the current player location.
     * @param GameController $gameController
     * @param string $itemTag
     * @param string $containerTag
     * @return Response
     * @throws PlayerLocationNotSetException
     */
    protected function takeItemsByTagFromFirstContainerByTagAtPlayerLocation(
        GameController $gameController,
        string $itemTag,
        string $containerTag,
    ): Response {
        $container = $this->getFirstContainerByTagAtPlayerLocation($gameController, $containerTag);

        if ($container instanceof ContainerInterface) {
            $items = $container->getItemsByTag($itemTag);

            $response = new Response();
            if (empty($items)) {
                $itemMessage = new UnableMessage($itemTag, UnableMessage::TYPE_ITEM_NOT_FOUND);
                $response->addMessage($itemMessage->toString());
                return $response;
            }

            foreach ($items as $item) {
                if ($item instanceof ItemInterface) {
                    if ($item->getDiscovered()) {
                        if ($item->getAccessible()) {
                            if ($item->getAcquirable()) {
                                $container->removeItemById($item->getId());

                                $addItemResponse = $this->addItemToPlayerInventory(
                                    $gameController,
                                    $item
                                );

                                $response->addMessages($addItemResponse->getMessages());
                            } else {
                                $message = new UnableMessage(
                                    $item->getName(),
                                    UnableMessage::TYPE_CANNOT_TAKE
                                );
                                $response->addMessage($message->toString());
                                return $response;
                            }
                        } else {
                            $message = new UnableMessage(
                                $itemTag,
                                UnableMessage::TYPE_ITEM_NOT_ACCESSIBLE
                            );
                            $response->addMessage($message->toString());
                            return $response;
                        }
                    } else {
                        $message = new UnableMessage(
                            $itemTag,
                            UnableMessage::TYPE_ITEM_NOT_DISCOVERED
                        );
                        $response->addMessage($message->toString());
                        return $response;
                    }
                }
            }

            return $response;
        }

        $response = new Response();
        $message = new UnableMessage($containerTag, UnableMessage::TYPE_CONTAINER_NOT_FOUND);
        $response->addMessage($message->toString());
        return $response;
    }

    /**
     * Get the first container by tag at current player location.
     * @param GameController $gameController
     * @param string $tag
     * @return ContainerInterface|null
     * @throws PlayerLocationNotSetException
     */
    protected function getFirstContainerByTagAtPlayerLocation(
        GameController $gameController,
        string $tag
    ): ?ContainerInterface {
        $location = $gameController->mapController->getPlayerLocation();

        $containers = $location->getContainer()->getItemsByTypeAndTag(
            ContainerInterface::class,
            $tag
        );

        if (count($containers) && $containers[0] instanceof ContainerInterface) {
            return $containers[0];
        }

        return null;
    }

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
        $itemMessage = new ItemMessage($item, ItemMessage::TYPE_ADD);
        $response->addMessage($itemMessage->toString());

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
            $itemMessage = new UnableMessage($tag, UnableMessage::TYPE_ITEM_NOT_FOUND);
            $response->addMessage($itemMessage->toString());
            return $response;
        }

        foreach ($this->describeItems($items) as $description) {
            $response->addItemDescription($description);
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
     * @param EntityInterface $entity
     * @param ItemInterface $key
     * @return string response message
     */
    protected function unlockEntityWithKey(EntityInterface $entity, ItemInterface $key): string
    {
        if (is_a($entity, LockableInterface::class)) {
            $entity->setLocked(false);
            $message = new LockableEntityMessage($entity, $key, LockableEntityMessage::TYPE_UNLOCK);
            return $message->toString();
        }

        $message = new UnableMessage(
            $entity->getName(),
            UnableMessage::TYPE_CANNOT_UNLOCK
        );
        return $message->toString();
    }
}