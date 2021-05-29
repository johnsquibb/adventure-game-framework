<?php

namespace AdventureGame\Command\Commands;

use AdventureGame\Command\CommandInterface;
use AdventureGame\Entity\ReadableEntityInterface;
use AdventureGame\Game\Exception\InvalidExitException;
use AdventureGame\Game\Exception\PlayerLocationNotSetException;
use AdventureGame\Game\GameController;
use AdventureGame\Item\ContainerItem;
use AdventureGame\Item\ContainerItemInterface;
use AdventureGame\Item\ItemInterface;
use AdventureGame\Location\Portal;
use AdventureGame\Response\ItemDescription;
use AdventureGame\Response\ListOfItems;
use AdventureGame\Response\Message\ItemMessage;
use AdventureGame\Response\Message\UnableMessage;
use AdventureGame\Response\Response;

/**
 * Class VerbNounCommand processes verb+noun commands, e.g. "take sword" or "drop dish".
 * @package AdventureGame\Command\Commands
 */
class VerbNounCommand extends AbstractCommand implements CommandInterface
{
    public function __construct(
        private string $verb,
        private string $noun
    ) {
    }

    /**
     * Process verb+noun action.
     * @param GameController $gameController
     * @return Response|null
     * @throws InvalidExitException
     * @throws PlayerLocationNotSetException
     */
    public function process(GameController $gameController): ?Response
    {
        if ($response = $this->tryMoveAction($gameController)) {
            return $response;
        }

        if ($response = $this->tryInventoryAction($gameController)) {
            return $response;
        }

        if ($response = $this->tryLocationItemAction($gameController)) {
            return $response;
        }

        if ($response = $this->tryLookAction($gameController)) {
            return $response;
        }

        return $this->tryKeyAction($gameController);
    }

    /**
     * Attempt to move player if the verb is a move action.
     * @param GameController $gameController
     * @return Response|null
     * @throws InvalidExitException
     * @throws PlayerLocationNotSetException
     */
    private function tryMoveAction(GameController $gameController): ?Response
    {
        switch ($this->verb) {
            case 'go':
                return $this->movePlayer($gameController, $this->noun);
        }

        return null;
    }

    /**
     * Try an inventory action with player's inventory items at player location.
     * @param GameController $gameController
     * @return Response|null
     * @throws PlayerLocationNotSetException
     */
    private function tryInventoryAction(GameController $gameController): ?Response
    {
        switch ($this->verb) {
            case 'drop':
                return $this->dropItemsByTagAtPlayerLocation($gameController, $this->noun);
            case 'activate':
                return $this->activateItemsByTagInPlayerInventory($gameController, $this->noun);
            case 'deactivate':
                return $this->deactivateItemsByTagInPlayerInventory($gameController, $this->noun);
            case 'read':
                return $this->readItemsByTagInPlayerInventory($gameController, $this->noun);
        }

        return null;
    }

    /**
     * Try an action on items at player location.
     * @param GameController $gameController
     * @return Response|null
     * @throws PlayerLocationNotSetException
     */
    private function tryLocationItemAction(GameController $gameController): ?Response
    {
        switch ($this->verb) {
            case 'take':
                return $this->takeItemsByTagAtPlayerLocation($gameController, $this->noun);
            case 'activate':
                return $this->activateItemsByTagAtPlayerLocation($gameController, $this->noun);
            case 'deactivate':
                return $this->deactivateItemsByTagAtPlayerLocation($gameController, $this->noun);
            case 'read':
                return $this->readItemsByTagAtPlayerLocation($gameController, $this->noun);
        }

        return null;
    }

    /**
     * Drop all items in player inventory matching tag to player location.
     * @param GameController $gameController
     * @param string $tag
     * @return Response
     * @throws PlayerLocationNotSetException
     */
    private function dropItemsByTagAtPlayerLocation(
        GameController $gameController,
        string $tag
    ): Response {
        $response = new Response();

        $items = $gameController->playerController->getItemsByTagFromPlayerInventory($tag);

        if (empty($items)) {
            $message = new UnableMessage($tag, UnableMessage::TYPE_ITEM_NOT_IN_INVENTORY);
            $response->addMessage($message->toString());
            return $response;
        }

        if (count($items) > 1) {
            $listOfItems = new ListOfItems($items, ListOfItems::ACTION_DROP);
            return $listOfItems->getResponse();
        }

        foreach ($items as $item) {
            if ($item instanceof ItemInterface) {
                $dropItemResponse = $this->removeItemFromPlayerInventory($gameController, $item);
                $gameController->mapController->dropItem($item);
                $response->addMessages($dropItemResponse->getMessages());
            }
        }

        return $response;
    }

    /**
     * Activate items by tag in player inventory.
     * @param GameController $gameController
     * @param string $tag
     * @return Response|null
     * @throws PlayerLocationNotSetException
     */
    private function activateItemsByTagInPlayerInventory(
        GameController $gameController,
        string $tag
    ): ?Response {
        $items = $gameController->playerController->getPlayerInventory()->getItemsByTag($tag);

        if (empty($items)) {
            return null;
        }

        return $this->activateItems($gameController, $items);
    }

    /**
     * Activate items.
     * @param GameController $gameController
     * @param array $items
     * @return Response
     * @throws PlayerLocationNotSetException
     */
    private function activateItems(GameController $gameController, array $items): Response
    {
        $response = new Response();

        if (count($items) > 1) {
            $listOfItems = new ListOfItems($items, ListOfItems::ACTION_ACTIVATE);
            return $listOfItems->getResponse();
        }

        foreach ($items as $item) {
            if ($item instanceof ItemInterface) {
                if ($item->getActivatable()) {
                    if ($item->getActivated() === true) {
                        $message = new UnableMessage(
                            $item->getName(),
                            UnableMessage::TYPE_ALREADY_ACTIVATED
                        );
                        $response->addMessage($message->toString());
                    } else {
                        $item->setActivated(true);
                        $message = new ItemMessage(
                            $item,
                            ItemMessage::TYPE_DEACTIVATE
                        );

                        $eventResponse = $gameController->eventController->processActivateItemEvents(
                            $gameController,
                            $item->getId()
                        );

                        if ($eventResponse) {
                            $response->addMessages($eventResponse->getMessages());
                        }
                    }

                    $response->addMessage($message->toString());
                } else {
                    $message = new UnableMessage(
                        $item->getName(),
                        UnableMessage::TYPE_CANNOT_DEACTIVATE
                    );
                    $response->addMessage($message->toString());
                }
            }
        }

        return $response;
    }

    /**
     * Deactivate items by tag in player inventory.
     * @param GameController $gameController
     * @param string $tag
     * @return Response|null
     */
    private function deactivateItemsByTagInPlayerInventory(
        GameController $gameController,
        string $tag
    ): ?Response {
        $items = $gameController->playerController->getPlayerInventory()->getItemsByTag($tag);

        if (empty($items)) {
            return null;
        }

        return $this->deactivateItems($gameController, $items);
    }

    private function deactivateItems(GameController $gameController, array $items): Response
    {
        $response = new Response();

        if (count($items) > 1) {
            $listOfItems = new ListOfItems($items, ListOfItems::ACTION_DEACTIVATE);
            return $listOfItems->getResponse();
        }

        foreach ($items as $item) {
            if ($item instanceof ItemInterface) {
                if ($item->getDeactivatable()) {
                    if ($item->getDeactivated() === true) {
                        $message = new UnableMessage(
                            $item->getName(),
                            UnableMessage::TYPE_ALREADY_DEACTIVATED
                        );
                        $response->addMessage($message->toString());
                    } else {
                        $item->setDeactivated(true);
                        $message = new ItemMessage(
                            $item,
                            ItemMessage::TYPE_DEACTIVATE
                        );

                        $eventResponse = $gameController->eventController->processDeactivateItemEvents(
                            $gameController,
                            $item->getId()
                        );

                        if ($eventResponse) {
                            $response->addMessages($eventResponse->getMessages());
                        }
                    }

                    $response->addMessage($message->toString());
                } else {
                    $message = new UnableMessage(
                        $item->getName(),
                        UnableMessage::TYPE_CANNOT_DEACTIVATE
                    );
                    $response->addMessage($message->toString());
                }
            }
        }

        return $response;
    }

    /**
     * Take all acquirable items matching tag at player location into player inventory.
     * @param GameController $gameController
     * @param string $tag
     * @return Response|null
     * @throws PlayerLocationNotSetException
     */
    private function takeItemsByTagAtPlayerLocation(
        GameController $gameController,
        string $tag
    ): ?Response {
        $response = new Response();

        $items = $gameController->mapController->getItemsByTag($tag);

        // If nothing in the location, try the first container.
        if (empty($items)) {
            $containers = $gameController
                ->getMapController()->getPlayerLocation()->getContainer()->getItems();

            foreach ($containers as $container) {
                if ($container instanceof ContainerItemInterface) {
                    $response = $this->takeItemsByTagFromFirstContainerByTagAtPlayerLocation(
                        $gameController,
                        $tag,
                        $container->getTags()[0]
                    );
                    if ($response instanceof Response) {
                        return $response;
                    }
                }
            }
        }

        if (count($items) > 1) {
            $listOfItems = new ListOfItems($items, ListOfItems::ACTION_TAKE);
            return $listOfItems->getResponse();
        }

        foreach ($items as $item) {
            if ($item instanceof ItemInterface) {
                if ($item->getAcquirable()) {
                    // Remove the item from map, add to player inventory.
                    $item = $gameController->mapController->takeItemById($item->getId());
                    $addItemResponse = $this->addItemToPlayerInventory($gameController, $item);
                    $response->addMessages($addItemResponse->getMessages());
                } else {
                    $message = new UnableMessage($item->getName(), UnableMessage::TYPE_CANNOT_TAKE);
                    $response->addMessage($message->toString());
                }
            }
        }

        return $response;
    }

    /**
     * Activate items by tag at player location.
     * @param GameController $gameController
     * @param string $tag
     * @return Response|null
     * @throws PlayerLocationNotSetException
     */
    private function activateItemsByTagAtPlayerLocation(
        GameController $gameController,
        string $tag
    ): ?Response {
        $items = $gameController->getMapController()->getItemsByTag($tag);

        if (empty($items)) {
            return null;
        }

        return $this->activateItems($gameController, $items);
    }

    /**
     * Deactivate items by tag at player location.
     * @param GameController $gameController
     * @param string $tag
     * @return Response|null
     * @throws PlayerLocationNotSetException
     */
    private function deactivateItemsByTagAtPlayerLocation(
        GameController $gameController,
        string $tag
    ): ?Response {
        $items = $gameController->getMapController()->getItemsByTag($tag);

        if (empty($items)) {
            return null;
        }

        return $this->deactivateItems($gameController, $items);
    }

    /**
     * Read items matching tag at player location.
     * @param GameController $gameController
     * @param string $tag
     * @return Response
     * @throws PlayerLocationNotSetException
     */
    private function readItemsByTagAtPlayerLocation(
        GameController $gameController,
        string $tag
    ): Response {
        $items = $gameController->mapController->getItemsByTag($tag);

        if (empty($items)) {
            $response = new Response();
            $message = new UnableMessage($tag, UnableMessage::TYPE_ITEM_NOT_FOUND);
            $response->addMessage($message->toString());
            return $response;
        }

        return $this->readItems($gameController, $items);
    }

    /**
     * Read items matching tag in player inventory.
     * @param GameController $gameController
     * @param string $tag
     * @return Response|null
     */
    private function readItemsByTagInPlayerInventory(
        GameController $gameController,
        string $tag
    ): ?Response {
        $items = $gameController->getPlayerController()->getPlayerInventory()->getItemsByTag($tag);

        if (empty($items)) {
            $response = new Response();
            $message = new UnableMessage($tag, UnableMessage::TYPE_ITEM_NOT_FOUND);
            $response->addMessage($message->toString());
            return $response;
        }

        return $this->readItems($gameController, $items);
    }

    /**
     * Attempt to look at objects, location.
     * @param GameController $gameController
     * @return Response|null
     * @throws PlayerLocationNotSetException
     */
    private function tryLookAction(GameController $gameController): ?Response
    {
        switch ($this->verb) {
            case 'look':
                return $this->tryLookAtItemsByTagAtPlayerLocationAction(
                    $gameController,
                    $this->noun
                );
        }

        return null;
    }

    /**
     * Try an action using a key from player inventory at player location.
     * @param GameController $gameController
     * @return Response|null
     * @throws PlayerLocationNotSetException
     */
    private function tryKeyAction(GameController $gameController): ?Response
    {
        switch ($this->verb) {
            case 'unlock':
                return $this->unlockEntitiesByTagAtPlayerLocation($gameController, $this->noun);
            case 'lock':
                return $this->lockEntitiesByTagAtPlayerLocation($gameController, $this->noun);
        }

        return null;
    }

    /**
     * Unlock entities by tag at player location.
     * @param GameController $gameController
     * @param string $noun
     * @return Response
     * @throws PlayerLocationNotSetException
     */
    private function unlockEntitiesByTagAtPlayerLocation(
        GameController $gameController,
        string $noun
    ): Response {
        $response = new Response();

        $location = $gameController->mapController->getPlayerLocation();

        // Try unlocking a door.
        $portal = $location->getExitByTag($noun);
        if ($portal instanceof Portal) {
            if ($portal->getMutable()) {
                $message = $this->unlockPortalWithAnyAvailableKey($gameController, $portal);
                $response->addMessage($message);
            } else {
                $unableMessage = new UnableMessage(
                    $portal->getName(),
                    UnableMessage::TYPE_CANNOT_UNLOCK
                );
                $response->addMessage($unableMessage->toString());
            }

            return $response;
        }

        // Try unlocking a container.
        $containers = $location->getContainer()
            ->getItemsByTypeAndTag(ContainerItem::class, $noun);

        if (empty($containers)) {
            $notFoundMessage = new UnableMessage($noun, UnableMessage::TYPE_NOTHING_TO_UNLOCK);
            $response->addMessage($notFoundMessage->toString());
            return $response;
        }

        foreach ($containers as $container) {
            if ($container->getMutable()) {
                $message = $this->unlockContainerItemWithAnyAvailableKey(
                    $gameController,
                    $container
                );
                $response->addMessage($message);
            } else {
                $unableMessage = new UnableMessage(
                    $container->getName(),
                    UnableMessage::TYPE_CANNOT_UNLOCK
                );
                $response->addMessage($unableMessage->toString());
                return $response;
            }
        }
    }

    /**
     * Unlock a portal if the player has the key in inventory.
     * @param GameController $gameController
     * @param Portal $portal
     * @return string
     */
    protected function unlockPortalWithAnyAvailableKey(
        GameController $gameController,
        Portal $portal
    ): string {
        $keyId = $portal->getKeyEntityId();

        $key = $gameController->playerController->getItemByIdFromPlayerInventory($keyId);
        if ($key instanceof ItemInterface) {
            return $this->unlockEntityWithKey($portal, $key);
        }

        $message = new UnableMessage(
            $portal->getName(), UnableMessage::TYPE_MISSING_KEY
        );

        return $message->toString();
    }

    /**
     * Unlock containerItems if the player has the key in inventory.
     * @param GameController $gameController
     * @param ContainerItemInterface $containerItem
     * @return string
     */
    protected function unlockContainerItemWithAnyAvailableKey(
        GameController $gameController,
        ContainerItemInterface $containerItem
    ): string {
        $keyId = $containerItem->getKeyEntityId();

        $key = $gameController->playerController->getItemByIdFromPlayerInventory($keyId);
        if ($key instanceof ItemInterface) {
            return $this->unlockEntityWithKey($containerItem, $key);
        }

        $message = new UnableMessage(
            $containerItem->getName(), UnableMessage::TYPE_MISSING_KEY
        );

        return $message->toString();
    }

    /**
     * Lock entities by tag at player location.
     * @param GameController $gameController
     * @param string $noun
     * @return Response
     * @throws PlayerLocationNotSetException
     */
    private function lockEntitiesByTagAtPlayerLocation(
        GameController $gameController,
        string $noun
    ): Response {
        $response = new Response();

        $location = $gameController->mapController->getPlayerLocation();

        // Try locking a door.
        $portal = $location->getExitByTag($noun);
        if ($portal instanceof Portal) {
            if ($portal->getMutable()) {
                $message = $this->lockPortalWithAnyAvailableKey($gameController, $portal);
                $response->addMessage($message);
            } else {
                $unableMessage = new UnableMessage(
                    $portal->getName(),
                    UnableMessage::TYPE_CANNOT_LOCK
                );
                $response->addMessage($unableMessage->toString());
            }

            return $response;
        }

        // Try locking a container.
        $containers = $location->getContainer()->getItemsByTypeAndTag(ContainerItem::class, $noun);

        if (empty($containers)) {
            $notFoundMessage = new UnableMessage($noun, UnableMessage::TYPE_NOTHING_TO_LOCK);
            $response->addMessage($notFoundMessage->toString());
            return $response;
        }

        foreach ($containers as $container) {
            if ($container instanceof ContainerItemInterface) {
                if ($container->getMutable()) {
                    $message = $this->LockContainerItemWithAnyAvailableKey(
                        $gameController,
                        $container
                    );
                    $response->addMessage($message);
                } else {
                    $unableMessage = new UnableMessage(
                        $container->getName(),
                        UnableMessage::TYPE_CANNOT_LOCK
                    );
                    $response->addMessage($unableMessage->toString());
                }
            }
        }

        return $response;
    }

    /**
     * Unlock a portal if the player has the key in inventory.
     * @param GameController $gameController
     * @param Portal $portal
     * @return string
     */
    protected function lockPortalWithAnyAvailableKey(
        GameController $gameController,
        Portal $portal
    ): string {
        $keyId = $portal->getKeyEntityId();

        $key = $gameController->playerController->getItemByIdFromPlayerInventory($keyId);
        if ($key instanceof ItemInterface) {
            return $this->lockEntityWithKey($portal, $key);
        }

        $message = new UnableMessage(
            $portal->getName(), UnableMessage::TYPE_MISSING_KEY
        );

        return $message->toString();
    }

    /**
     * Unlock containerItems if the player has the key in inventory.
     * @param GameController $gameController
     * @param ContainerItemInterface $containerItem
     * @return string
     */
    protected function LockContainerItemWithAnyAvailableKey(
        GameController $gameController,
        ContainerItemInterface $containerItem
    ): string {
        $keyId = $containerItem->getKeyEntityId();

        $key = $gameController->playerController->getItemByIdFromPlayerInventory($keyId);
        if ($key instanceof ItemInterface) {
            return $this->lockEntityWithKey($containerItem, $key);
        }

        $message = new UnableMessage(
            $containerItem->getName(), UnableMessage::TYPE_MISSING_KEY
        );

        return $message->toString();
    }

    /**
     * Read items.
     * @param GameController $gameController
     * @param array $items
     * @return Response
     */
    private function readItems(GameController $gameController, array $items): Response
    {
        $response = new Response();

        if (count($items) > 1) {
            $listOfItems = new ListOfItems($items, ListOfItems::ACTION_READ);
            return $listOfItems->getResponse();
        }

        foreach ($items as $item) {
            if ($item instanceof ReadableEntityInterface) {
                if ($item->getReadable()) {
                    $response->addMessages($item->getLines());
                } else {
                    if ($item instanceof ItemDescription) {
                        $message = new UnableMessage(
                            $item->getName(),
                            UnableMessage::TYPE_CANNOT_READ
                        );
                        $response->addMessage($message->toString());
                    }
                }
            }
        }

        return $response;
    }
}