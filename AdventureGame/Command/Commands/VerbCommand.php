<?php

namespace AdventureGame\Command\Commands;

use AdventureGame\Command\CommandInterface;
use AdventureGame\Game\Exception\InvalidExitException;
use AdventureGame\Game\Exception\PlayerLocationNotSetException;
use AdventureGame\Game\GameController;
use AdventureGame\IO\OutputController;
use AdventureGame\Location\Direction;
use AdventureGame\Location\Location;

class VerbCommand implements CommandInterface
{
    public function __construct(
        private string $verb,
        private OutputController $outputController,
    ) {
    }

    /**
     * Process verb action.
     * @param GameController $gameController
     * @throws InvalidExitException
     * @throws PlayerLocationNotSetException
     */
    public function process(GameController $gameController): void
    {
        $this->tryMoveAction($gameController) || $this->tryLookAction($gameController);
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

    /**
     * Move player, describe the new location.
     * @param GameController $gameController
     * @param $direction
     * @throws InvalidExitException
     * @throws PlayerLocationNotSetException
     */
    private function movePlayer(GameController $gameController, $direction): void
    {
        $gameController->mapController->movePlayer($direction);
        $this->describePlayerLocation($gameController);
    }

    /**
     * Describe the current player location.
     * @param GameController $gameController
     * @throws PlayerLocationNotSetException
     */
    private function describePlayerLocation(GameController $gameController): void
    {
        $lines = $this->describeLocation($gameController->mapController->getPlayerLocation());
        $this->outputController->addLines($lines);
    }

    /**
     * Describe a location.
     * @param Location $location
     * @return array
     */
    private function describeLocation(Location $location): array
    {
        return [
            $location->name,
            $location->description,
        ];
    }
}