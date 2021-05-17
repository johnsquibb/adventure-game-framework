<?php

namespace AdventureGame\Command;

use AdventureGame\Game\GameController;
use AdventureGame\Response\Response;

/**
 * Interface CommandInterface defines methods for Commands.
 * @package AdventureGame\Command
 */
interface CommandInterface
{
    public function process(GameController $gameController): ?Response;
}