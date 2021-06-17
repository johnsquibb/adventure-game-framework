<?php

namespace AdventureGame\Event;

use AdventureGame\Game\GameController;
use AdventureGame\Response\Response;

interface TriggerInterface
{
    public function getId(): string;

    public function execute(GameController $gameController): ?Response;
}