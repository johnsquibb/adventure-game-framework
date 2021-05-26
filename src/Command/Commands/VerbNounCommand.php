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
     * Try an inventory action with player's inventory items at current player location.
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
                // TODO read from player's inventory.
                return null;
        }

        return null;
    }

    /**
     * Try an action on items at current player location.
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
     * Take all acquirable items matching tag at current player location into player inventory.
     * @param GameController $gameController
     * @param string $tag
     * @return Response
     * @throws PlayerLocationNotSetException
     */
    private function takeItemsByTagAtPlayerLocation(
        GameController $gameController,
        string $tag
    ): Response {
        $response = new Response();

        $items = $gameController->mapController->getItemsByTag($tag);

        if (empty($items)) {
            $response->addMessage("You don't see anything like that here.");
        }

        if (count($items) > 1) {
            $response->addMessage('Which item do you want to take?');
            foreach ($items as $item) {
                $response->addItemSummaryWithTag($this->listItem($item));
            }
            return $response;
        }

        foreach ($items as $item) {
            if ($item instanceof ItemInterface) {
                if ($item->getAcquirable()) {
                    // Remove the item from map, add to player inventory.
                    $item = $gameController->mapController->takeItemById($item->getId());
                    $addItemResponse = $this->addItemToPlayerInventory($gameController, $item);
                    $response->addMessages($addItemResponse->getMessages());
                } else {
                    $response->addMessage("You can't take {$item->getName()}.");
                }
            }
        }

        return $response;
    }

    /**
     * Drop all items in player inventory matching tag to current player location.
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
            $response->addMessage("You don't have anything like that.");
            return $response;
        }

        if (count($items) > 1) {
            $response->addMessage('Which item do you want to drop?');
            foreach ($items as $item) {
                $response->addItemSummaryWithTag($this->listItem($item));
            }
            return $response;
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
     * Read all readable items matching tag at current player location into player inventory.
     * @param GameController $gameController
     * @param string $tag
     * @return Response
     * @throws PlayerLocationNotSetException
     */
    private function readItemsByTagAtPlayerLocation(
        GameController $gameController,
        string $tag
    ): Response {
        $response = new Response();

        $items = $gameController->mapController->getItemsByTag($tag);

        if (empty($items)) {
            $response->addMessage("You don't have anything like that to read.");
        }

        if (count($items) > 1) {
            $response->addMessage('Which item do you want to read?');
            foreach ($items as $item) {
                $response->addItemSummaryWithTag($this->listItem($item));
            }
            return $response;
        }

        foreach ($items as $item) {
            if ($item instanceof ReadableEntityInterface) {
                if ($item->getReadable()) {
                    $response->addMessages($item->getLines());
                } else {
                    $response->addMessage("You can't read {$item->getName()}.");
                }
            }
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
                $response->addMessage("You can't unlock {$portal->getName()}");
            }

            return $response;
        }

        // Try unlocking a container.
        $containers = $location->getContainer()
            ->getItemsByTypeAndTag(ContainerItem::class, $noun);

        if (empty($containers)) {
            $response->addMessage("There is nothing to unlock.");
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
                $response->addMessage("You can't unlock {$container->getName()}");
            }
        }
    }

    /**
     * Unlock a portal if the player has the key in inventory.
     * @param GameController $gameController
     * @param Portal $portal
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

        return "You don't have the required key.";
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

        return "You don't have the required key.";
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
                $response->addMessage("You can't lock {$portal->getName()}");
            }

            return $response;
        }

        // Try locking a container.
        $containers = $location->getContainer()->getItemsByTypeAndTag(ContainerItem::class, $noun);

        if (empty($containers)) {
            $response->addMessage("There is nothing to lock.");
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
                    $response->addMessage("You can't lock {$container->getName()}");
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

        return "You don't have the required key.";
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

        return "You don't have the required key.";
    }

    /**
     * Activate items by tag in player inventory.
     * @param GameController $gameController
     * @param string $tag
     * @return Response
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
     * Deactivate items by tag in player inventory.
     * @param GameController $gameController
     * @param string $tag
     * @return Response
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

    private function activateItems(GameController $gameController, array $items): Response
    {
        $response = new Response();

        if (count($items) > 1) {
            $response->addMessage('Which item do you want to activate?');
            foreach ($items as $item) {
                $response->addItemSummaryWithTag($this->listItem($item));
            }
            return $response;
        }

        foreach ($items as $item) {
            if ($item instanceof ItemInterface) {
                if ($item->getActivatable()) {
                    if ($item->getActivated() === true) {
                        $message = "It's already activated.";
                    } else {
                        $item->setActivated(true);
                        $message = "Activated {$item->getName()}.";

                        $eventResponse = $gameController->eventController->processActivateItemEvents(
                            $gameController,
                            $item->getId()
                        );

                        if ($eventResponse) {
                            $response->addMessages($eventResponse->getMessages());
                        }
                    }

                    $response->addMessage($message);
                } else {
                    $response->addMessage("You can't activate {$item->getName()}.");
                }
            }
        }

        return $response;
    }

    private function deactivateItems(GameController $gameController, array $items): Response
    {
        $response = new Response();

        if (count($items) > 1) {
            $response->addMessage('Which item do you want to deactivate?');
            foreach ($items as $item) {
                $response->addItemSummaryWithTag($this->listItem($item));
            }
            return $response;
        }

        foreach ($items as $item) {
            if ($item instanceof ItemInterface) {
                if ($item->getDeactivatable()) {
                    if ($item->getDeactivated() === true) {
                        $message = "It's already deactivated.";
                    } else {
                        $item->setDeactivated(true);
                        $message = "Deactivated {$item->getName()}.";

                        $eventResponse = $gameController->eventController->processDeactivateItemEvents(
                            $gameController,
                            $item->getId()
                        );

                        if ($eventResponse) {
                            $response->addMessages($eventResponse->getMessages());
                        }
                    }

                    $response->addMessage($message);
                } else {
                    $response->addMessage("You can't deactivate {$item->getName()}.");
                }
            }
        }

        return $response;
    }
}