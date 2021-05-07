<?php

namespace AdventureGame\Command\Commands;

use AdventureGame\Game\Exception\InvalidExitException;
use AdventureGame\Game\Exception\PlayerLocationNotSetException;
use AdventureGame\Game\GameController;
use AdventureGame\IO\OutputController;
use AdventureGame\Location\Location;

abstract class AbstractCommand
{
    public function __construct(
        protected OutputController $outputController,
    ) {
    }

    /**
     * Move player, describe the new location.
     * @param GameController $gameController
     * @param $direction
     * @throws InvalidExitException
     * @throws PlayerLocationNotSetException
     */
    protected function movePlayer(GameController $gameController, $direction): void
    {
        $gameController->mapController->movePlayer($direction);
        $this->describePlayerLocation($gameController);
    }

    /**
     * Describe the current player location.
     * @param GameController $gameController
     * @throws PlayerLocationNotSetException
     */
    protected function describePlayerLocation(GameController $gameController): void
    {
        $lines = $this->describeLocation($gameController->mapController->getPlayerLocation());
        $this->outputController->addLines($lines);
    }

    /**
     * Describe a location.
     * @param Location $location
     * @return array
     */
    protected function describeLocation(Location $location): array
    {
        return [
            $location->name,
            $location->description,
        ];
    }
}