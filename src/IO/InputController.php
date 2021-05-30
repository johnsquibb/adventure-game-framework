<?php

namespace AdventureGame\IO;

use AdventureGame\Command\CommandController;
use AdventureGame\Command\CommandParser;
use AdventureGame\Command\Exception\InvalidCommandException;
use AdventureGame\Command\Exception\InvalidNounException;
use AdventureGame\Command\Exception\InvalidPrepositionException;
use AdventureGame\Command\Exception\InvalidTokensLengthException;
use AdventureGame\Command\Exception\InvalidVerbException;
use AdventureGame\Game\Exception\PlayerLocationNotSetException;
use AdventureGame\Game\GameController;
use AdventureGame\Response\Response;

/**
 * Class InputController interacts with parser, controller to build and execute commands from player
 * input.
 * @package AdventureGame\IO
 */
class InputController extends InputOutputController implements InputOutputControllerInterface
{
    public function __construct(
        private CommandParser $commandParser,
        private CommandController $commandController,
        private GameController $gameController,
    ) {
    }

    /**
     * Create command from string input and run it.
     * Successful processing will be relayed through downstream components, whereas exceptions will
     * throw back to the caller.
     * @param string $input
     * @return Response|null
     * @throws InvalidCommandException
     * @throws InvalidNounException
     * @throws InvalidPrepositionException
     * @throws InvalidTokensLengthException
     * @throws InvalidVerbException|PlayerLocationNotSetException
     */
    public function processInput(string $input): ?Response
    {
        $tokens = $this->convertInputToTokens($input);
        return $this->commandController->createAndProcessCommandFromTokens($tokens);
    }

    /**
     * Convert string input to tokens usable in commands.
     * @param string $input
     * @return array
     * @throws InvalidCommandException|PlayerLocationNotSetException
     */
    private function convertInputToTokens(string $input): array
    {
        $input = strtolower($input);

        $command = $this->commandParser->applyShortcuts($input);
        $command = $this->applyPhrases($command);
        $tokens = $this->commandParser->parseCommand($command);
        $tokens = $this->commandParser->normalizeTokens($tokens);
        $tokens = $this->commandParser->filterTokens($tokens);
        $tokens = $this->commandParser->replaceAliases($tokens);

        if (!$this->commandParser->validateTokens($tokens)) {
            throw new InvalidCommandException('invalid command');
        }

        return $tokens;
    }

    /**
     * Apply phrases.
     * @param string $command
     * @return string
     * @throws PlayerLocationNotSetException
     */
    private function applyPhrases(string $command): string
    {
        $command = $this->applyLocationPhrases($command);
        return $this->commandParser->applyPhrases($command);
    }

    /**
     * Apply location-specific phrases.
     * @param string $command
     * @return string
     * @throws PlayerLocationNotSetException
     */
    private function applyLocationPhrases(string $command): string
    {
        $locationId = $this->gameController->getMapController()->getPlayerLocation()->getId();
        $locationPhrases = $this->commandParser->getLocationPhrases();
        if (isset($locationPhrases[$locationId])) {
            $command = $this->commandParser->applyLocationPhrases($command, $locationId);
        }

        return $command;
    }
}