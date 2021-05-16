<?php

namespace AdventureGame\Command\Commands;

use AdventureGame\Command\CommandInterface;
use AdventureGame\Game\Exception\PlayerLocationNotSetException;
use AdventureGame\Game\GameController;
use AdventureGame\IO\OutputController;
use AdventureGame\Item\ContainerInterface;
use AdventureGame\Item\ContainerItem;
use AdventureGame\Item\ItemInterface;
use AdventureGame\Location\Portal;

class VerbNounPrepositionNounCommand extends AbstractCommand implements CommandInterface
{
    public function __construct(
        private string $verb,
        private string $noun1,
        private string $preposition,
        private string $noun2,
        OutputController $outputController,
    ) {
        parent::__construct($outputController);
    }

    /**
     * Process verb+noun+preposition+noun action.
     * @param GameController $gameController
     * @return bool
     * @throws PlayerLocationNotSetException
     */
    public function process(GameController $gameController): bool
    {
        return $this->tryContainerItemAction($gameController) || $this->tryKeyAction(
                $gameController
            );
    }

    /**
     * Try an item action involving a container at the current player's location.
     * @param GameController $gameController
     * @return bool true if a take verb was processed, false otherwise.
     * @throws PlayerLocationNotSetException
     */
    private function tryContainerItemAction(GameController $gameController): bool
    {
        switch ($this->verb) {
            case 'take':
                $this->takeItemsByTagFromFirstContainerByTagAtPlayerLocation(
                    $gameController,
                    $this->noun1,
                    $this->noun2
                );
                return true;
            case 'drop':
            case 'put':
                $this->dropItemsByTagIntoFirstContainerByTagAtPlayerLocation(
                    $gameController,
                    $this->noun1,
                    $this->noun2
                );
                return true;
        }

        return false;
    }

    /**
     * Take all the items by tag from container matching another tag at the current player location.
     * @param GameController $gameController
     * @param string $itemTag
     * @param string $containerTag
     * @throws PlayerLocationNotSetException
     */
    private function takeItemsByTagFromFirstContainerByTagAtPlayerLocation(
        GameController $gameController,
        string $itemTag,
        string $containerTag,
    ): void {
        $container = $this->getFirstContainerByTagAtPlayerLocation($gameController, $containerTag);

        if ($container) {
            $items = $container->getItemsByTag($itemTag);

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
                    if ($item->getAccessible()) {
                        $this->addItemToPlayerInventory($gameController, $item);
                        $container->removeItemById($item->getId());
                    } else {
                        $this->outputController->addLines(
                            [
                                "You haven't discovered anything like that here."
                            ]
                        );
                    }
                }
            }
        }
    }

    /**
     * Get the first container by tag at current player location.
     * @param GameController $gameController
     * @param string $tag
     * @return ContainerInterface|null
     * @throws PlayerLocationNotSetException
     */
    private function getFirstContainerByTagAtPlayerLocation(
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
     * Drop all items matching tag from player inventory into the first container matching another
     * tag at current player location.
     * @param GameController $gameController
     * @param string $itemTag
     * @param string $containerTag
     * @throws PlayerLocationNotSetException
     */
    private function dropItemsByTagIntoFirstContainerByTagAtPlayerLocation(
        GameController $gameController,
        string $itemTag,
        string $containerTag,
    ): void {
        $container = $this->getFirstContainerByTagAtPlayerLocation($gameController, $containerTag);

        if ($container) {
            $items = $gameController->playerController->getItemsByTagFromPlayerInventory($itemTag);
            foreach ($items as $item) {
                $container->addItem($item);
                $this->removeItemFromPlayerInventory($gameController, $item);
            }
        }
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
                $this->unlockEntitiesByTagAtUsingKeyByTagAtPlayerLocation(
                    $gameController,
                    $this->noun1,
                    $this->noun2
                );
                return true;
            case 'lock':
                $this->lockEntitiesByTagAtUsingKeyByTagAtPlayerLocation(
                    $gameController,
                    $this->noun1,
                    $this->noun2
                );
                return true;
        }

        return false;
    }

    /**
     * Unlock entities by tag using key by tag at player location.
     * @param GameController $gameController
     * @param string $entityTag
     * @param string $keyTag
     * @throws PlayerLocationNotSetException
     */
    private function unlockEntitiesByTagAtUsingKeyByTagAtPlayerLocation(
        GameController $gameController,
        string $entityTag,
        string $keyTag
    ): void {
        $location = $gameController->mapController->getPlayerLocation();

        $keys = $gameController->playerController->getItemsByTagFromPlayerInventory($keyTag);
        if (empty($keys)) {
            $this->outputController->addLines(["You don't have {$keyTag}."]);
            return;
        }

        // Use the first available key.
        $key = $keys[0];

        // Try unlocking a door.
        $portal = $location->getExitByTag($entityTag);
        if ($portal instanceof Portal) {
            if ($portal->getMutable()) {
                $this->unlockEntityWithKey($portal, $key);
            } else {
                $this->outputController->addLines(
                    ["You can't unlock {$portal->getName()} with {$keyTag}"]
                );
            }
            return;
        }

        // Try unlocking a container.
        $containers = $location->getContainer()->getItemsByTypeAndTag(
            ContainerItem::class,
            $entityTag
        );

        if (empty($containers)) {
            $this->outputController->addLines(["The is nothing to unlock with {$keyTag}."]);
            return;
        }

        foreach ($containers as $container) {
            if ($container->getMutable()) {
                $this->unlockEntityWithKey($container, $key);
            } else {
                $this->outputController->addLines(["You can't unlock {$container->getName()}"]);
            }
        }
    }

    /**
     * Lock entities by tag using key by tag at player location.
     * @param GameController $gameController
     * @param string $entityTag
     * @param string $keyTag
     * @throws PlayerLocationNotSetException
     */
    private function lockEntitiesByTagAtUsingKeyByTagAtPlayerLocation(
        GameController $gameController,
        string $entityTag,
        string $keyTag
    ): void {
        $location = $gameController->mapController->getPlayerLocation();

        $keys = $gameController->playerController->getItemsByTagFromPlayerInventory($keyTag);
        if (empty($keys)) {
            $this->outputController->addLines(["You don't have {$keyTag}."]);
            return;
        }

        // Use the first available key.
        $key = $keys[0];

        // Try locking a door.
        $portal = $location->getExitByTag($entityTag);
        if ($portal instanceof Portal) {
            if ($portal->getMutable()) {
                $this->lockEntityWithKey($portal, $key);
            } else {
                $this->outputController->addLines(
                    ["You can't lock {$portal->getName()} with {$keyTag}"]
                );
            }
            return;
        }

        // Try locking a container.
        $containers = $location->getContainer()->getItemsByTypeAndTag(
            ContainerItem::class,
            $entityTag
        );

        if (empty($containers)) {
            $this->outputController->addLines(["The is nothing to lock with {$keyTag}."]);
            return;
        }

        foreach ($containers as $container) {
            if ($container->getMutable()) {
                $this->lockEntityWithKey($container, $key);
            } else {
                $this->outputController->addLines(["You can't lock {$container->getName()}"]);
            }
        }
    }
}