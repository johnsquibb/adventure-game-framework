<?php

namespace AdventureGame\Command\Commands;

use AdventureGame\Command\CommandInterface;
use AdventureGame\Game\Exception\PlayerLocationNotSetException;
use AdventureGame\Game\GameController;
use AdventureGame\IO\OutputController;
use AdventureGame\Item\ItemInterface;

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
     * @throws PlayerLocationNotSetException
     */
    public function process(GameController $gameController): bool
    {
        return $this->tryInventoryAction($gameController);
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
     * Take all items matching tag at current player location into player inventory.
     * @param GameController $gameController
     * @param string $tag
     * @throws PlayerLocationNotSetException
     */
    protected function takeItemsByTagAtPlayerLocation(
        GameController $gameController,
        string $tag
    ): void {
        $items = $gameController->mapController->takeItemsByTag($tag);

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
    protected function dropItemsByTagAtPlayerLocation(
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
}