<?php

namespace AdventureGame\Game;

use AdventureGame\Command\CommandFactory;
use AdventureGame\Command\CommandInterface;

class CommandController
{
    public function __construct(
        private CommandFactory $commandFactory,
        private GameController $gameController,
    ) {
    }

    public function createAndProcessCommandFromTokens(array $tokens): bool
    {
        $command = $this->createCommandFromTokens($tokens);

        return $this->processCommand($command);
    }

    private function createCommandFromTokens(array $tokens): CommandInterface
    {
        return $this->commandFactory->createFromTokens($tokens);
    }

    private function processCommand(CommandInterface $command): bool
    {
        return $command->process($this->gameController);
    }
}