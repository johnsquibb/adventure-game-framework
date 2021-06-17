<?php

namespace AdventureGame\Event;

use AdventureGame\Game\GameController;
use AdventureGame\Response\Response;

interface TriggerInterface
{
    public function getId(): string;

    public function setId(string $id): void;

    public function execute(GameController $gameController): ?Response;
}