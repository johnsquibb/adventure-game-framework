<?php

namespace AdventureGame\Command;

use AdventureGame\Game\GameController;

interface CommandInterface
{
    public function process(GameController $gameController): void;
}