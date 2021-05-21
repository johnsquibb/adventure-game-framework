<?php

namespace AdventureGame\Event;

use AdventureGame\Game\GameController;
use AdventureGame\Response\Response;

interface EventInterface
{
    public function trigger(GameController $gameController): ?Response;
}