<?php

namespace AdventureGame\Command\Commands;

use AdventureGame\Command\CommandInterface;
use AdventureGame\Game\Exception\PlayerLocationNotSetException;
use AdventureGame\Game\GameController;
use AdventureGame\IO\OutputController;
use AdventureGame\Item\ContainerInterface;
use AdventureGame\Item\ItemInterface;

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
        return $this->tryContainerItemAction($gameController);
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
            foreach ($items as $item) {
                if ($item instanceof ItemInterface) {
                    $this->addItemToPlayerInventory($gameController, $item);
                    $container->removeItemById($item->id);
                }
            }
        }
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

        $containers = $location->items->getItemsByTypeAndTag(
            ContainerInterface::class,
            $tag
        );

        if (count($containers) && $containers[0] instanceof ContainerInterface) {
            return $containers[0];
        }

        return null;
    }
}