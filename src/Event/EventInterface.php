<?php

namespace AdventureGame\Event;

use AdventureGame\Game\GameController;

interface EventInterface
{
    public function trigger(GameController $gameController): void;
}