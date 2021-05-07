<?php

namespace AdventureGame\Command;

use AdventureGame\Command\Commands\VerbCommand;
use AdventureGame\Command\Commands\VerbNounCommand;
use AdventureGame\Command\Commands\VerbNounPrepositionNounCommand;
use AdventureGame\Command\Commands\VerbPrepositionNounCommand;
use AdventureGame\Command\Exception\InvalidCommandException;
use AdventureGame\IO\OutputController;

class CommandFactory
{
    public function __construct(
        private CommandParser $commandParser,
        private OutputController $outputController
    ) {
    }

    /**
     * Factory a command from tokens.
     * @param array $tokens
     * @return CommandInterface
     * @throws InvalidCommandException
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
    }

    /**
     * Create verb command.
     * @param string $verb
     * @return CommandInterface
     * @throws InvalidCommandException
     */
    private function factoryVerbCommand(string $verb): CommandInterface
    {
        if (!$this->commandParser->isVerb($verb)) {
            throw new InvalidCommandException('invalid verb');
        }

        return new VerbCommand($verb, $this->outputController);
    }

    /**
     * Create verb+noun command.
     * @param string $verb
     * @param string $noun
     * @return CommandInterface
     * @throws InvalidCommandException
     */
    private function factoryVerbNounCommand(string $verb, string $noun): CommandInterface
    {
        if (!$this->commandParser->isVerb($verb)) {
            throw new InvalidCommandException('invalid verb');
        }

        if (!$this->commandParser->isNoun($noun)) {
            throw new InvalidCommandException('invalid noun');
        }

        return new VerbNounCommand($verb, $noun, $this->outputController);
    }

    /**
     * Create verb+preposition+noun command.
     * @param string $verb
     * @param string $preposition
     * @param string $noun
     * @return CommandInterface
     * @throws InvalidCommandException
     */
    private function factoryVerbPrepositionNounCommand(
        string $verb,
        string $preposition,
        string $noun
    ): CommandInterface {
        if (!$this->commandParser->isVerb($verb)) {
            throw new InvalidCommandException('invalid verb');
        }

        if (!$this->commandParser->isPreposition($preposition)) {
            throw new InvalidCommandException('invalid preposition');
        }

        if (!$this->commandParser->isNoun($noun)) {
            throw new InvalidCommandException('invalid noun');
        }

        return new VerbPrepositionNounCommand($verb, $preposition, $noun, $this->outputController);
    }

    /**
     * Create verb+noun+preposition+noun command.
     * @param string $verb
     * @param string $noun1
     * @param string $preposition
     * @param string $noun2
     * @return CommandInterface
     * @throws InvalidCommandException
     */
    private function factoryVerbNounPrepositionNounCommand(
        string $verb,
        string $noun1,
        string $preposition,
        string $noun2
    ): CommandInterface {
        if (!$this->commandParser->isVerb($verb)) {
            throw new InvalidCommandException('invalid verb');
        }

        if (!$this->commandParser->isPreposition($preposition)) {
            throw new InvalidCommandException('invalid preposition');
        }

        if (!$this->commandParser->isNoun($noun1) || !$this->commandParser->isNoun($noun2)) {
            throw new InvalidCommandException('invalid noun');
        }

        return new VerbNounPrepositionNounCommand(
            $verb,
            $noun1,
            $preposition,
            $noun2,
            $this->outputController
        );
    }
}