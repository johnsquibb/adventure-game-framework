<?php

namespace AdventureGame\Event;

use AdventureGame\Game\GameController;

interface TriggerInterface
{
    public function execute(GameController $gameController): void;
}