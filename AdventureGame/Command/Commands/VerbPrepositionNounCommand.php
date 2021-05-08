<?php

namespace AdventureGame\Command\Commands;

use AdventureGame\Command\CommandInterface;
use AdventureGame\Game\Exception\PlayerLocationNotSetException;
use AdventureGame\Game\GameController;
use AdventureGame\IO\OutputController;

/**
 * Class VerbPrepositionNounCommand processes verb+preposition+noun commands, e.g. "look at spoon".
 * @package AdventureGame\Command\Commands
 */
class VerbPrepositionNounCommand extends AbstractCommand implements CommandInterface
{
    public function __construct(
        private string $verb,
        private string $preposition,
        private string $noun,
        OutputController $outputController,
    ) {
        parent::__construct($outputController);
    }

    /**
     * Process verb+noun action.
     * @param GameController $gameController
     * @throws PlayerLocationNotSetException
     */
    public function process(GameController $gameController): void
    {
        $this->tryLookAction($gameController);
    }

    /**
     * Attempt to look at objects.
     * @param GameController $gameController
     * @return bool returns true when a look action is processed, false otherwise.
     * @throws PlayerLocationNotSetException
     */
    private function tryLookAction(GameController $gameController): bool
    {
        switch ($this->verb) {
            case 'look':
                return $this->tryLookAtItemsAtPlayerLocationAction($gameController, $this->noun);
        }

        return false;
    }

    /**
     * Try to look at items in the current player location.
     * @param GameController $gameController
     * @param string $tag
     * @return bool returns true when a look at item action is processed, false otherwise.
     * @throws PlayerLocationNotSetException
     */
    private function tryLookAtItemsAtPlayerLocationAction(
        GameController $gameController,
        string $tag
    ): bool {
        $items = $gameController->mapController->getPlayerLocation()->items->getItemsByTag($tag);
        if (count($items)) {
            $this->describeItems($items);
            return true;
        }

        return false;
    }
}