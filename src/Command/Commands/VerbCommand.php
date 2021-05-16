<?php

namespace AdventureGame\Command\Commands;

use AdventureGame\Command\CommandInterface;
use AdventureGame\Game\Exception\ExitIsLockedException;
use AdventureGame\Game\Exception\InvalidExitException;
use AdventureGame\Game\Exception\PlayerLocationNotSetException;
use AdventureGame\Game\GameController;
use AdventureGame\IO\OutputController;
use AdventureGame\Location\Direction;

/**
 * Class VerbCommand processes single-word verb commands, e.g. "take" or "eat".
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
     * @throws PlayerLocationNotSetException
     */
    public function process(GameController $gameController): bool
    {
        return $this->tryLookAction($gameController);
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