<?php

namespace AdventureGame\Game;

use AdventureGame\Command\CommandFactory;
use AdventureGame\Command\CommandInterface;
use AdventureGame\Command\Exception\InvalidNounException;
use AdventureGame\Command\Exception\InvalidPrepositionException;
use AdventureGame\Command\Exception\InvalidTokensLengthException;
use AdventureGame\Command\Exception\InvalidVerbException;

/**
 * Class CommandController creates and processes commands from tokens.
 * @package AdventureGame\Game
 */
class CommandController
{
    public function __construct(
        public CommandFactory $commandFactory,
        private GameController $gameController,
    ) {
    }

    /**
     * Create and process a command from array of tokens.
     * @param array $tokens
     * @return bool true when a command was successfully processed, false otherwise.
     * @throws InvalidNounException
     * @throws InvalidPrepositionException
     * @throws InvalidTokensLengthException
     * @throws InvalidVerbException
     */
    public function createAndProcessCommandFromTokens(array $tokens): bool
    {
        $command = $this->createCommandFromTokens($tokens);
        return $this->processCommand($command);
    }

    /**
     * Create a command from array of tokens.
     * @param array $tokens
     * @return CommandInterface
     * @throws InvalidNounException
     * @throws InvalidPrepositionException
     * @throws InvalidTokensLengthException
     * @throws InvalidVerbException
     */
    private function createCommandFromTokens(array $tokens): CommandInterface
    {
        return $this->commandFactory->createFromTokens($tokens);
    }

    /**
     * Process a command.
     * @param CommandInterface $command
     * @return bool true when a command was successfully processed, false otherwise.
     */
    private function processCommand(CommandInterface $command): bool
    {
        return $command->process($this->gameController);
    }
}