<?php

namespace AdventureGame\Command\Commands;

use AdventureGame\Command\CommandInterface;
use AdventureGame\Game\Exception\PlayerLocationNotSetException;
use AdventureGame\Game\GameController;
use AdventureGame\Item\ContainerInterface;
use AdventureGame\Item\ContainerItem;
use AdventureGame\Item\ItemInterface;
use AdventureGame\Location\Portal;
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
            case 'take':
                return $this->takeItemsByTagFromFirstContainerByTagAtPlayerLocation(
                    $gameController,
                    $this->noun1,
                    $this->noun2
                );
            case 'drop':
            case 'put':
                return $this->dropItemsByTagIntoFirstContainerByTagAtPlayerLocation(
                    $gameController,
                    $this->noun1,
                    $this->noun2
                );
        }

        return null;
    }

    /**
     * Take all the items by tag from container matching another tag at the current player location.
     * @param GameController $gameController
     * @param string $itemTag
     * @param string $containerTag
     * @return Response
     * @throws PlayerLocationNotSetException
     */
    private function takeItemsByTagFromFirstContainerByTagAtPlayerLocation(
        GameController $gameController,
        string $itemTag,
        string $containerTag,
    ): Response {
        $response = new Response();

        $container = $this->getFirstContainerByTagAtPlayerLocation($gameController, $containerTag);

        if ($container) {
            $items = $container->getItemsByTag($itemTag);

            if (empty($items)) {
                $response->addMessage("You don't see anything like that here.");
                return $response;
            }

            foreach ($items as $item) {
                if ($item instanceof ItemInterface) {
                    if ($item->getAccessible()) {
                        if ($item->getAcquirable()) {
                            $container->removeItemById($item->getId());

                            $addItemResponse = $this->addItemToPlayerInventory(
                                $gameController,
                                $item
                            );

                            $response->addMessages($addItemResponse->getMessages());
                        } else {
                            $response->addMessage("You can't take that.");
                        }
                    } else {
                        $response->addMessage("You haven't discovered anything like that here.");
                    }
                }
            }
        }

        return $response;
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
     * @return Response
     * @throws PlayerLocationNotSetException
     */
    private function dropItemsByTagIntoFirstContainerByTagAtPlayerLocation(
        GameController $gameController,
        string $itemTag,
        string $containerTag,
    ): Response {
        $response = new Response();

        $container = $this->getFirstContainerByTagAtPlayerLocation($gameController, $containerTag);

        if ($container) {
            $items = $gameController->playerController->getItemsByTagFromPlayerInventory($itemTag);

            if (empty($items)) {
                $response->addMessage("You don't have that.");
            }

            foreach ($items as $item) {
                $container->addItem($item);
                $removeItemResponse = $this->removeItemFromPlayerInventory($gameController, $item);
                $response->addMessages($removeItemResponse->getMessages());
            }
        } else {
            $response->addMessage("Can't put that there.");
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
                return $this->unlockEntitiesByTagAtUsingKeyByTagAtPlayerLocation(
                    $gameController,
                    $this->noun1,
                    $this->noun2
                );
            case 'lock':
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
            $response->addMessage("You don't have {$keyTag}.");
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
                $response->addMessage("You can't unlock {$portal->getName()} with {$keyTag}");
            }
            return $response;
        }

        // Try unlocking a container.
        $containers = $location->getContainer()->getItemsByTypeAndTag(
            ContainerItem::class,
            $entityTag
        );

        if (empty($containers)) {
            $response->addMessage("There is nothing to unlock with {$keyTag}.");
            return $response;
        }

        foreach ($containers as $container) {
            if ($container->getMutable()) {
                $message = $this->unlockEntityWithKey($container, $key);
                $response->addMessage($message);
            } else {
                $response->addMessage("You can't unlock {$container->getName()}");
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
            $response->addMessage("You don't have {$keyTag}.");
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
                $response->addMessage("You can't lock {$portal->getName()} with {$keyTag}");
            }
            return $response;
        }

        // Try locking a container.
        $containers = $location->getContainer()->getItemsByTypeAndTag(
            ContainerItem::class,
            $entityTag
        );

        if (empty($containers)) {
            $response->addMessage("The is nothing to lock with {$keyTag}.");
            return $response;
        }

        foreach ($containers as $container) {
            if ($container->getMutable()) {
                $message = $this->lockEntityWithKey($container, $key);
                $response->addMessage($message);
            } else {
                $response->addMessage("You can't lock {$container->getName()}");
            }
        }

        return $response;
    }
}