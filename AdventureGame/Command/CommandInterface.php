<?php

namespace AdventureGame\Command;

use AdventureGame\Game\GameController;

/**
 * Interface CommandInterface defines methods for Commands.
 * @package AdventureGame\Command
 */
interface CommandInterface
{
    public function process(GameController $gameController): bool;
}