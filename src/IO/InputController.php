<?php

namespace AdventureGame\IO;

use AdventureGame\Command\CommandParser;
use AdventureGame\Command\Exception\InvalidCommandException;
use AdventureGame\Command\Exception\InvalidNounException;
use AdventureGame\Command\Exception\InvalidPrepositionException;
use AdventureGame\Command\Exception\InvalidTokensLengthException;
use AdventureGame\Command\Exception\InvalidVerbException;
use AdventureGame\Command\CommandController;

/**
 * Class InputController handles input text.
 * @package AdventureGame\IO
 */
class InputController extends InputOutputController implements InputOutputControllerInterface
{
    public function __construct(
        private CommandParser $commandParser,
        public CommandController $commandController
    ) {
    }

    /**
     * Create command from string input and run it.
     * Successful processing will be relayed through downstream components, whereas exceptions will
     * throw back to the caller.
     * @param string $input
     * @throws InvalidCommandException
     * @throws InvalidNounException
     * @throws InvalidPrepositionException
     * @throws InvalidTokensLengthException
     * @throws InvalidVerbException
     */
    public function processInput(string $input)
    {
        $tokens = $this->convertInputToTokens($input);
        $this->commandController->createAndProcessCommandFromTokens($tokens);
    }

    /**
     * Convert string input to tokens usable in commands.
     * @param string $input
     * @return array
     * @throws InvalidCommandException
     */
    private function convertInputToTokens(string $input): array
    {
        $tokens = $this->commandParser->parseCommand($input);
        $tokens = $this->commandParser->normalizeTokens($tokens);
        $tokens = $this->commandParser->filterTokens($tokens);
        $tokens = $this->commandParser->replaceAliases($tokens);

        if (!$this->commandParser->validateTokens($tokens)) {
            throw new InvalidCommandException('invalid command');
        }

        return $tokens;
    }
}