<?php

namespace AdventureGame\Event;

use AdventureGame\Game\GameController;
use AdventureGame\Response\Response;

interface EventInterface
{
    public function getId(): string;

    public function setId(string $id): void;

    public function trigger(GameController $gameController): ?Response;
}
