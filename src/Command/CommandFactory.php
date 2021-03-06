<?php

namespace AdventureGame\Command;

use AdventureGame\Command\Commands\VerbCommand;
use AdventureGame\Command\Commands\VerbNounCommand;
use AdventureGame\Command\Commands\VerbNounPrepositionNounCommand;
use AdventureGame\Command\Commands\VerbPrepositionNounCommand;
use AdventureGame\Command\Exception\InvalidNounException;
use AdventureGame\Command\Exception\InvalidPrepositionException;
use AdventureGame\Command\Exception\InvalidTokensLengthException;
use AdventureGame\Command\Exception\InvalidVerbException;
use AdventureGame\IO\OutputController;

/**
 * Class CommandFactory builds processable Commands from arrays of token strings.
 * @package AdventureGame\Command
 */
class CommandFactory
{
    public function __construct(
        private CommandParser $commandParser,
        public OutputController $outputController
    ) {
    }

    /**
     * Factory a command from tokens.
     * @param array $tokens
     * @return CommandInterface
     * @throws InvalidNounException
     * @throws InvalidPrepositionException
     * @throws InvalidTokensLengthException
     * @throws InvalidVerbException
     */
    public function createFromTokens(array $tokens): CommandInterface
    {
        switch (count($tokens)) {
            case 1:
                return $this->factoryVerbCommand(...$tokens);
            case 2:
                return $this->factoryVerbNounCommand(...$tokens);
            case 3:
                return $this->factoryVerbPrepositionNounCommand(...$tokens);
            case 4:
                return $this->factoryVerbNounPrepositionNounCommand(...$tokens);
        }

        throw new InvalidTokensLengthException('invalid tokens length');
    }

    /**
     * Create verb command.
     * @param string $verb
     * @return CommandInterface
     * @throws InvalidVerbException
     */
    private function factoryVerbCommand(string $verb): CommandInterface
    {
        if (!$this->commandParser->isVerb($verb)) {
            throw new InvalidVerbException('invalid verb');
        }

        return new VerbCommand($verb);
    }

    /**
     * Create verb+noun command.
     * @param string $verb
     * @param string $noun
     * @return CommandInterface
     * @throws InvalidNounException
     * @throws InvalidVerbException
     */
    private function factoryVerbNounCommand(string $verb, string $noun): CommandInterface
    {
        if (!$this->commandParser->isVerb($verb)) {
            throw new InvalidVerbException('invalid verb');
        }

        if (!$this->commandParser->isNoun($noun)) {
            throw new InvalidNounException('invalid noun');
        }

        return new VerbNounCommand($verb, $noun);
    }

    /**
     * Create verb+preposition+noun command.
     * @param string $verb
     * @param string $preposition
     * @param string $noun
     * @return CommandInterface
     * @throws InvalidNounException
     * @throws InvalidPrepositionException
     * @throws InvalidVerbException
     */
    private function factoryVerbPrepositionNounCommand(
        string $verb,
        string $preposition,
        string $noun
    ): CommandInterface {
        if (!$this->commandParser->isVerb($verb)) {
            throw new InvalidVerbException('invalid verb');
        }

        if (!$this->commandParser->isPreposition($preposition)) {
            throw new InvalidPrepositionException('invalid preposition');
        }

        if (!$this->commandParser->isNoun($noun)) {
            throw new InvalidNounException('invalid noun');
        }

        return new VerbPrepositionNounCommand($verb, $preposition, $noun);
    }

    /**
     * Create verb+noun+preposition+noun command.
     * @param string $verb
     * @param string $noun1
     * @param string $preposition
     * @param string $noun2
     * @return CommandInterface
     * @throws InvalidNounException
     * @throws InvalidPrepositionException
     * @throws InvalidVerbException
     */
    private function factoryVerbNounPrepositionNounCommand(
        string $verb,
        string $noun1,
        string $preposition,
        string $noun2
    ): CommandInterface {
        if (!$this->commandParser->isVerb($verb)) {
            throw new InvalidVerbException('invalid verb');
        }

        if (!$this->commandParser->isPreposition($preposition)) {
            throw new InvalidPrepositionException('invalid preposition');
        }

        if (!$this->commandParser->isNoun($noun1) || !$this->commandParser->isNoun($noun2)) {
            throw new InvalidNounException('invalid noun');
        }

        return new VerbNounPrepositionNounCommand(
            $verb,
            $noun1,
            $preposition,
            $noun2
        );
    }
}
