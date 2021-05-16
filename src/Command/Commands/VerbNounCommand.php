<?php

namespace AdventureGame\Command\Commands;

use AdventureGame\Command\CommandInterface;
use AdventureGame\Game\Exception\InvalidExitException;
use AdventureGame\Game\Exception\PlayerLocationNotSetException;
use AdventureGame\Game\GameController;
use AdventureGame\IO\OutputController;
use AdventureGame\Item\ContainerItem;
use AdventureGame\Item\ContainerItemInterface;
use AdventureGame\Item\ItemInterface;
use AdventureGame\Location\Direction;
use AdventureGame\Location\Portal;

/**
 * Class VerbNounCommand processes verb+noun commands, e.g. "take sword" or "drop dish".
 * @package AdventureGame\Command\Commands
 */
class VerbNounCommand extends AbstractCommand implements CommandInterface
{
    public function __construct(
        private string $verb,
        private string $noun,
        OutputController $outputController,
    ) {
        parent::__construct($outputController);
    }

    /**
     * Process verb+noun action.
     * @param GameController $gameController
     * @return bool
     * @throws PlayerLocationNotSetException|InvalidExitException
     */
    public function process(GameController $gameController): bool
    {
        return $this->tryMoveAction($gameController)
            || $this->tryInventoryAction($gameController)
            || $this->tryKeyAction($gameController);
    }

    /**
     * Attempt to move player if the verb is a move action.
     * @param GameController $gameController
     * @return bool true if a move verb was processed, false otherwise.
     * @throws InvalidExitException
     * @throws PlayerLocationNotSetException
     */
    private function tryMoveAction(GameController $gameController): bool
    {
        switch ($this->verb) {
            case 'go':
                $this->movePlayer($gameController, $this->noun);
                return true;
        }

        return false;
    }

    /**
     * Try an inventory action on player's inventory at current player location.
     * @param GameController $gameController
     * @return bool true if a take verb was processed, false otherwise.
     * @throws PlayerLocationNotSetException
     */
    private function tryInventoryAction(GameController $gameController): bool
    {
        switch ($this->verb) {
            case 'take':
                $this->takeItemsByTagAtPlayerLocation($gameController, $this->noun);
                return true;
            case 'drop':
                $this->dropItemsByTagAtPlayerLocation($gameController, $this->noun);
                return true;
        }

        return false;
    }

    /**
     * Try an action using a key from player inventory at current player location.
     * @param GameController $gameController
     * @return bool true if a key action was processed, false otherwise.
     * @throws PlayerLocationNotSetException
     */
    private function tryKeyAction(GameController $gameController): bool
    {
        switch ($this->verb) {
            case 'unlock':
                $this->unlockEntitiesByTagAtPlayerLocation($gameController, $this->noun);
                return true;
            case 'lock':
                $this->lockEntitiesByTagAtPlayerLocation($gameController, $this->noun);
                return true;
        }

        return false;
    }

    /**
     * Take all items matching tag at current player location into player inventory.
     * @param GameController $gameController
     * @param string $tag
     * @throws PlayerLocationNotSetException
     */
    private function takeItemsByTagAtPlayerLocation(
        GameController $gameController,
        string $tag
    ): void {
        $items = $gameController->mapController->takeItemsByTag($tag);

        if (empty($items)) {
            $this->outputController->addLines(
                [
                    "You don't see anything like that here."
                ]
            );
            return;
        }

        foreach ($items as $item) {
            if ($item instanceof ItemInterface) {
                $this->addItemToPlayerInventory($gameController, $item);
            }
        }
    }

    /**
     * Drop all items in player inventory matching tag to current player location.
     * @param GameController $gameController
     * @param string $tag
     * @throws PlayerLocationNotSetException
     */
    private function dropItemsByTagAtPlayerLocation(
        GameController $gameController,
        string $tag
    ): void {
        $items = $gameController->playerController->getItemsByTagFromPlayerInventory($tag);

        foreach ($items as $item) {
            if ($item instanceof ItemInterface) {
                $this->removeItemFromPlayerInventory($gameController, $item);
                $gameController->mapController->dropItem($item);
            }
        }
    }

    /**
     * Unlock entities by tag at player location.
     * @param GameController $gameController
     * @param string $noun
     * @throws PlayerLocationNotSetException
     */
    private function unlockEntitiesByTagAtPlayerLocation(
        GameController $gameController,
        string $noun
    ): void {
        $location = $gameController->mapController->getPlayerLocation();

        // Try unlocking a door.
        $portal = $location->getExitByTag($noun);
        if ($portal instanceof Portal) {
            if ($portal->getMutable()) {
                $this->unlockPortalWithAnyAvailableKey($gameController, $portal);
            } else {
                $this->outputController->addLines(["You can't unlock {$portal->getName()}"]);
            }
            return;
        }

        // Try unlocking a container.
        $containers = $location->getContainer()->getItemsByTypeAndTag(ContainerItem::class, $noun);

        if (empty($containers)) {
            $this->outputController->addLines(["The is nothing to unlock."]);
            return;
        }

        foreach ($containers as $container) {
            if ($container->getMutable()) {
                $this->unlockContainerItemWithAnyAvailableKey($gameController, $container);
            } else {
                $this->outputController->addLines(["You can't unlock {$container->getName()}"]);
            }
        }
    }

    private function lockEntitiesByTagAtPlayerLocation(GameController $gameController, string $noun)
    {
        $location = $gameController->mapController->getPlayerLocation();

        // Try locking a door.
        $portal = $location->getExitByTag($noun);
        if ($portal instanceof Portal) {
            if ($portal->getMutable()) {
                $this->lockPortalWithAnyAvailableKey($gameController, $portal);
            } else {
                $this->outputController->addLines(["You can't lock {$portal->getName()}"]);
            }
            return;
        }

        // Try locking a container.
        $containers = $location->getContainer()->getItemsByTypeAndTag(ContainerItem::class, $noun);

        if (empty($containers)) {
            $this->outputController->addLines(["The is nothing to lock."]);
            return;
        }

        foreach ($containers as $container) {
            if ($container instanceof ContainerItemInterface) {
                if ($container->getMutable()) {
                    $this->LockContainerItemWithAnyAvailableKey($gameController, $container);
                } else {
                    $this->outputController->addLines(["You can't lock {$container->getName()}"]);
                }
            }
        }
    }

    /**
     * Unlock containerItems if the player has the key in inventory.
     * @param GameController $gameController
     * @param ContainerItemInterface $containerItem
     */
    protected function unlockContainerItemWithAnyAvailableKey(
        GameController $gameController,
        ContainerItemInterface $containerItem
    ): void {
        $keyId = $containerItem->getKeyEntityId();

        $key = $gameController->playerController->getItemByIdFromPlayerInventory($keyId);
        if ($key instanceof ItemInterface) {
            $this->unlockEntityWithKey($containerItem, $key);
        } else {
            $this->outputController->addLines(
                [
                    "You don't have the required key.",
                ]
            );
        }
    }

    /**
     * Unlock containerItems if the player has the key in inventory.
     * @param GameController $gameController
     * @param ContainerItemInterface $containerItem
     */
    protected function LockContainerItemWithAnyAvailableKey(
        GameController $gameController,
        ContainerItemInterface $containerItem
    ): void {
        $keyId = $containerItem->getKeyEntityId();

        $key = $gameController->playerController->getItemByIdFromPlayerInventory($keyId);
        if ($key instanceof ItemInterface) {
            $this->lockEntityWithKey($containerItem, $key);
        } else {
            $this->outputController->addLines(
                [
                    "You don't have the required key.",
                ]
            );
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
    ): void {
        $keyId = $portal->getKeyEntityId();

        $key = $gameController->playerController->getItemByIdFromPlayerInventory($keyId);
        if ($key instanceof ItemInterface) {
            $this->unlockEntityWithKey($portal, $key);
        } else {
            $this->outputController->addLines(
                [
                    "You don't have the required key.",
                ]
            );
        }
    }

    /**
     * Unlock a portal if the player has the key in inventory.
     * @param GameController $gameController
     * @param Portal $portal
     */
    protected function lockPortalWithAnyAvailableKey(
        GameController $gameController,
        Portal $portal
    ): void {
        $keyId = $portal->getKeyEntityId();

        $key = $gameController->playerController->getItemByIdFromPlayerInventory($keyId);
        if ($key instanceof ItemInterface) {
            $this->lockEntityWithKey($portal, $key);
        } else {
            $this->outputController->addLines(
                [
                    "You don't have the required key.",
                ]
            );
        }
    }
}