<?php

namespace AdventureGame\Command\Commands;

use AdventureGame\Command\CommandInterface;
use AdventureGame\Game\Exception\InvalidExitException;
use AdventureGame\Game\Exception\PlayerLocationNotSetException;
use AdventureGame\Game\GameController;
use AdventureGame\IO\OutputController;
use AdventureGame\Location\Direction;

/**
 * Class VerbCommand processes single-word verb commands, e.g. "take" or "eat".
 * It also processes words that imply more complex commands, e.g. "north" for "go north".
 * @package AdventureGame\Command\Commands
 */
class VerbCommand extends AbstractCommand implements CommandInterface
{
    public function __construct(
        private string $verb,
        OutputController $outputController,
    ) {
        parent::__construct($outputController);
    }

    /**
     * Process verb action.
     * @param GameController $gameController
     * @return bool
     * @throws InvalidExitException
     * @throws PlayerLocationNotSetException
     */
    public function process(GameController $gameController): bool
    {
        return $this->tryMoveAction($gameController) || $this->tryLookAction($gameController);
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
            case 'n':
            case 'north':
                $this->movePlayer($gameController, Direction::NORTH);
                return true;
            case 'e':
            case 'east':
                $this->movePlayer($gameController, Direction::EAST);
                return true;
            case 's':
            case 'south':
                $this->movePlayer($gameController, Direction::SOUTH);
                return true;
            case 'w':
            case 'west':
                $this->movePlayer($gameController, Direction::WEST);
                return true;
        }

        return false;
    }

    /**
     * Look at current player area.
     * @param GameController $gameController
     * @return bool
     * @throws PlayerLocationNotSetException
     */
    private function tryLookAction(GameController $gameController): bool
    {
        switch ($this->verb) {
            case 'look':
                $this->describePlayerLocation($gameController);
                return true;
        }

        return false;
    }
}