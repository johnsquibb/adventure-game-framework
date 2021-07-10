<?php

namespace AdventureGame\Command\Commands;

use AdventureGame\Command\CommandInterface;
use AdventureGame\Game\Exception\PlayerLocationNotSetException;
use AdventureGame\Game\GameController;
use AdventureGame\Item\ContainerItem;
use AdventureGame\Item\ContainerItemInterface;
use AdventureGame\Item\ItemInterface;
use AdventureGame\Location\Portal;
use AdventureGame\Response\Message\ContainerMessage;
use AdventureGame\Response\Message\UnableMessage;
use AdventureGame\Response\Response;

class VerbNounPrepositionNounCommand extends AbstractCommand implements CommandInterface
{
    public function __construct(
        private string $verb,
        private string $noun1,
        private string $preposition,
        private string $noun2
    ) {
    }

    /**
     * Process verb+noun+preposition+noun action.
     * @param GameController $gameController
     * @return Response|null
     * @throws PlayerLocationNotSetException
     */
    public function process(GameController $gameController): ?Response
    {
        if ($response = $this->tryContainerItemAction($gameController)) {
            return $response;
        }

        return $this->tryKeyAction($gameController);
    }

    /**
     * Try an item action involving a container at the current player's location.
     * @param GameController $gameController
     * @return Response|null
     * @throws PlayerLocationNotSetException
     */
    private function tryContainerItemAction(GameController $gameController): ?Response
    {
        switch ($this->verb) {
            case self::COMMAND_TAKE:
                return $this->takeItemsFromFirstContainerByTagAtPlayerLocation(
                    $gameController,
                    $this->noun1,
                    $this->noun2
                );
            case self::COMMAND_DROP:
            case self::COMMAND_PUT:
                return $this->dropItemsIntoFirstContainerByTagAtPlayerLocation(
                    $gameController,
                    $this->noun1,
                    $this->noun2
                );
        }

        return null;
    }

    /**
     * Take items from first container at location.
     * @param GameController $gameController
     * @param string $itemTag
     * @param string $containerTag
     * @return Response
     * @throws PlayerLocationNotSetException
     */
    private function takeItemsFromFirstContainerByTagAtPlayerLocation(
        GameController $gameController,
        string $itemTag,
        string $containerTag
    ): Response {
        return match ($itemTag) {
            self::NOUN_EVERYTHING => $this->takeAllItemsFromFirstContainerByTagAtPlayerLocation(
                $gameController,
                $itemTag,
                $containerTag
            ),
            default => $this->takeItemsByTagFromFirstContainerByTagAtPlayerLocation(
                $gameController,
                $this->noun1,
                $this->noun2
            ),
        };
    }

    /**
     * Drop items from player inventory into first container found by tag at player location.
     * @param GameController $gameController
     * @param string $itemTag
     * @param string $containerTag
     * @return Response
     * @throws PlayerLocationNotSetException
     */
    private function dropItemsIntoFirstContainerByTagAtPlayerLocation(
        GameController $gameController,
        string $itemTag,
        string $containerTag
    ): Response {
        return match ($itemTag) {
            self::NOUN_EVERYTHING => $this->dropAllItemsIntoFirstContainerByTagAtPlayerLocation(
                $gameController,
                $itemTag,
                $containerTag
            ),
            default => $this->dropItemsByTagIntoFirstContainerByTagAtPlayerLocation(
                $gameController,
                $this->noun1,
                $this->noun2
            ),
        };
    }

    /**
     * Drop all items matching tag from player inventory into the first container matching another
     * tag at current player location.
     * @param GameController $gameController
     * @param string $itemTag
     * @param string $containerTag
     * @return Response
     * @throws PlayerLocationNotSetException
     */
    private function dropItemsByTagIntoFirstContainerByTagAtPlayerLocation(
        GameController $gameController,
        string $itemTag,
        string $containerTag,
    ): Response {
        $response = new Response();

        $items = $gameController->playerController->getItemsByTagFromPlayerInventory($itemTag);

        if (empty($items)) {
            $message = new UnableMessage($itemTag, UnableMessage::TYPE_ITEM_NOT_IN_INVENTORY);
            $response->addMessage($message->toString());
            return $response;
        }

        return $this->dropItemsIntoContainerByTag($gameController, $containerTag, $items);
    }

    /**
     * Drop all items in player inventory into container by tag.
     * @param GameController $gameController
     * @param string $itemTag
     * @param string $containerTag
     * @return Response
     * @throws PlayerLocationNotSetException
     */
    private function dropAllItemsIntoFirstContainerByTagAtPlayerLocation(
        GameController $gameController,
        string $itemTag,
        string $containerTag
    ) {
        $response = new Response();

        $items = $gameController->playerController->getPlayerInventory()->getItems();
        if (empty($items)) {
            $message = new UnableMessage($itemTag, UnableMessage::TYPE_ITEM_NOT_IN_INVENTORY);
            $response->addMessage($message->toString());
            return $response;
        }

        return $this->dropItemsIntoContainerByTag($gameController, $containerTag, $items);
    }

    /**
     * Drop items into container by tag.
     * @param GameController $gameController
     * @param string $containerTag
     * @param array $items
     * @return Response
     * @throws PlayerLocationNotSetException
     */
    private function dropItemsIntoContainerByTag(
        GameController $gameController,
        string $containerTag,
        array $items
    ): Response {
        $response = new Response();

        $container = $this->getFirstContainerByTagAtPlayerLocation($gameController, $containerTag);

        if ($container instanceof ContainerItemInterface) {
            foreach ($items as $item) {
                if ($item instanceof ItemInterface) {
                    if ($container->hasCapacity($item->getSize())) {
                        $container->addItem($item);
                        $removeItemResponse = $this->removeItemFromPlayerInventory(
                            $gameController,
                            $item
                        );
                        $response->addMessages($removeItemResponse->getMessages());
                    } else {
                        $message = new ContainerMessage(
                            $container->getName(),
                            ContainerMessage::TYPE_CONTAINER_FULL
                        );
                        $response->addMessage($message->toString());
                        return $response;
                    }
                }
            }
        } else {
            $message = new UnableMessage($containerTag, UnableMessage::TYPE_CONTAINER_NOT_FOUND);
            $response->addMessage($message->toString());
            return $response;
        }

        return $response;
    }

    /**
     * Try an action using a key from player inventory at current player location.
     * @param GameController $gameController
     * @return Response|null
     * @throws PlayerLocationNotSetException
     */
    private function tryKeyAction(GameController $gameController): ?Response
    {
        switch ($this->verb) {
            case self::COMMAND_UNLOCK:
                return $this->unlockEntitiesByTagAtUsingKeyByTagAtPlayerLocation(
                    $gameController,
                    $this->noun1,
                    $this->noun2
                );
            case self::COMMAND_LOCK:
                return $this->lockEntitiesByTagAtUsingKeyByTagAtPlayerLocation(
                    $gameController,
                    $this->noun1,
                    $this->noun2
                );
        }

        return null;
    }

    /**
     * Unlock entities by tag using key by tag at player location.
     * @param GameController $gameController
     * @param string $entityTag
     * @param string $keyTag
     * @return Response
     * @throws PlayerLocationNotSetException
     */
    private function unlockEntitiesByTagAtUsingKeyByTagAtPlayerLocation(
        GameController $gameController,
        string $entityTag,
        string $keyTag
    ): Response {
        $response = new Response();


        $location = $gameController->mapController->getPlayerLocation();

        $keys = $gameController->playerController->getItemsByTagFromPlayerInventory($keyTag);
        if (empty($keys)) {
            $message = new UnableMessage($keyTag, UnableMessage::TYPE_ITEM_NOT_IN_INVENTORY);
            $response->addMessage($message->toString());
            return $response;
        }

        // Use the first available key.
        $key = $keys[0];

        // Try unlocking a door.
        $portal = $location->getExitByTag($entityTag);
        if ($portal instanceof Portal) {
            if ($portal->getMutable()) {
                $message = $this->unlockEntityWithKey($portal, $key);
                $response->addMessage($message);
            } else {
                $message = new UnableMessage(
                    $portal->getName(),
                    UnableMessage::TYPE_PORTAL_NOT_UNLOCKABLE
                );
                $response->addMessage($message->toString());
            }
            return $response;
        }

        // Try unlocking a container.
        $containers = $location->getContainer()->getItemsByTypeAndTag(
            ContainerItem::class,
            $entityTag
        );

        if (empty($containers)) {
            $message = new UnableMessage($keyTag, UnableMessage::TYPE_CONTAINER_NOT_FOUND);
            $response->addMessage($message->toString());
            return $response;
        }

        foreach ($containers as $container) {
            if ($container->getMutable()) {
                $message = $this->unlockEntityWithKey($container, $key);
                $response->addMessage($message);
            } else {
                $message = new UnableMessage($keyTag, UnableMessage::TYPE_CONTAINER_NOT_UNLOCKABLE);
                $response->addMessage($message->toString());
            }
        }

        return $response;
    }

    /**
     * Lock entities by tag using key by tag at player location.
     * @param GameController $gameController
     * @param string $entityTag
     * @param string $keyTag
     * @return Response
     * @throws PlayerLocationNotSetException
     */
    private function lockEntitiesByTagAtUsingKeyByTagAtPlayerLocation(
        GameController $gameController,
        string $entityTag,
        string $keyTag
    ): Response {
        $response = new Response();

        $location = $gameController->mapController->getPlayerLocation();

        $keys = $gameController->playerController->getItemsByTagFromPlayerInventory($keyTag);
        if (empty($keys)) {
            $message = new UnableMessage($keyTag, UnableMessage::TYPE_ITEM_NOT_IN_INVENTORY);
            $response->addMessage($message->toString());
            return $response;
        }

        // Use the first available key.
        $key = $keys[0];

        // Try locking a door.
        $portal = $location->getExitByTag($entityTag);
        if ($portal instanceof Portal) {
            if ($portal->getMutable()) {
                $message = $this->lockEntityWithKey($portal, $key);
                $response->addMessage($message);
            } else {
                $message = new UnableMessage(
                    $portal->getName(),
                    UnableMessage::TYPE_PORTAL_NOT_LOCKABLE
                );
                $response->addMessage($message->toString());
            }
            return $response;
        }

        // Try locking a container.
        $containers = $location->getContainer()->getItemsByTypeAndTag(
            ContainerItem::class,
            $entityTag
        );

        if (empty($containers)) {
            $message = new UnableMessage($keyTag, UnableMessage::TYPE_CONTAINER_NOT_FOUND);
            $response->addMessage($message->toString());
            return $response;
        }

        foreach ($containers as $container) {
            if ($container->getMutable()) {
                $message = $this->lockEntityWithKey($container, $key);
                $response->addMessage($message);
            } else {
                $message = new UnableMessage($keyTag, UnableMessage::TYPE_CONTAINER_NOT_LOCKABLE);
                $response->addMessage($message->toString());
            }
        }

        return $response;
    }
}
