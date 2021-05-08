<?php

namespace AdventureGame\Test\Command;

use AdventureGame\Command\CommandFactory;
use AdventureGame\Command\CommandParser;
use AdventureGame\Command\Commands\VerbCommand;
use AdventureGame\Command\Commands\VerbNounCommand;
use AdventureGame\Command\Commands\VerbNounPrepositionNounCommand;
use AdventureGame\Command\Commands\VerbPrepositionNounCommand;
use AdventureGame\Command\Exception\InvalidCommandException;
use AdventureGame\Command\Exception\InvalidNounException;
use AdventureGame\Command\Exception\InvalidPrepositionException;
use AdventureGame\Command\Exception\InvalidTokensLengthException;
use AdventureGame\Command\Exception\InvalidVerbException;
use AdventureGame\IO\OutputController;
use PHPUnit\Framework\TestCase;

class CommandFactoryTest extends TestCase
{
    private function createCommandParser(): CommandParser
    {
        $verbs = ['north', 'take', 'look', 'put'];
        $nouns = ['sword', 'sheath'];
        $articles = [];
        $prepositions = ['at', 'into'];
        $aliases = [];

        return new CommandParser($verbs, $nouns, $articles, $prepositions, $aliases);
    }

    public function testCreateVerbCommandFromTokens()
    {
        $commandParser = $this->createCommandParser();
        $outputController = new OutputController();
        $commandFactory = new CommandFactory($commandParser, $outputController);
        $command = $commandFactory->createFromTokens(['north']);

        $this->assertInstanceOf(VerbCommand::class, $command);
    }

    public function testCreateVerbCommandFromInvalidVerbToken()
    {
        $commandParser = $this->createCommandParser();
        $outputController = new OutputController();
        $commandFactory = new CommandFactory($commandParser, $outputController);
        $this->expectException(InvalidVerbException::class);
        $commandFactory->createFromTokens(['into']);
    }

    public function testCreateVerbNounCommandFromTokens()
    {
        $commandParser = $this->createCommandParser();
        $outputController = new OutputController();
        $commandFactory = new CommandFactory($commandParser, $outputController);
        $command = $commandFactory->createFromTokens(['take', 'sword']);

        $this->assertInstanceOf(VerbNounCommand::class, $command);
    }

    public function testCreateVerbNounCommandFromInvalidVerbToken()
    {
        $commandParser = $this->createCommandParser();
        $outputController = new OutputController();
        $commandFactory = new CommandFactory($commandParser, $outputController);
        $this->expectException(InvalidVerbException::class);
        $commandFactory->createFromTokens(['sword', 'take']);
    }

    public function testCreateVerbNounCommandFromInvalidNounToken()
    {
        $commandParser = $this->createCommandParser();
        $outputController = new OutputController();
        $commandFactory = new CommandFactory($commandParser, $outputController);
        $this->expectException(InvalidNounException::class);
        $commandFactory->createFromTokens(['take', 'take']);
    }

    public function testCreateVerbPrepositionNounCommandFromTokens()
    {
        $commandParser = $this->createCommandParser();
        $outputController = new OutputController();
        $commandFactory = new CommandFactory($commandParser, $outputController);
        $command = $commandFactory->createFromTokens(['look', 'at', 'sword']);

        $this->assertInstanceOf(VerbPrepositionNounCommand::class, $command);
    }

    public function testCreateVerbPrepositionNounCommandFromInvalidVerbToken()
    {
        $commandParser = $this->createCommandParser();
        $outputController = new OutputController();
        $commandFactory = new CommandFactory($commandParser, $outputController);
        $this->expectException(InvalidVerbException::class);
        $commandFactory->createFromTokens(['sword', 'at', 'sword']);
    }

    public function testCreateVerbPrepositionNounCommandFromInvalidPrepositionToken()
    {
        $commandParser = $this->createCommandParser();
        $outputController = new OutputController();
        $commandFactory = new CommandFactory($commandParser, $outputController);
        $this->expectException(InvalidPrepositionException::class);
        $commandFactory->createFromTokens(['look', 'sword', 'sword']);
    }

    public function testCreateVerbPrepositionNounCommandFromInvalidNounToken()
    {
        $commandParser = $this->createCommandParser();
        $outputController = new OutputController();
        $commandFactory = new CommandFactory($commandParser, $outputController);
        $this->expectException(InvalidNounException::class);
        $commandFactory->createFromTokens(['look', 'at', 'look']);
    }

    public function testCreateVerbNounPrepositionNounCommandFromTokens()
    {
        $commandParser = $this->createCommandParser();
        $outputController = new OutputController();
        $commandFactory = new CommandFactory($commandParser, $outputController);
        $command = $commandFactory->createFromTokens(['put', 'sword', 'into', 'sheath']);

        $this->assertInstanceOf(VerbNounPrepositionNounCommand::class, $command);
    }

    public function testCreateVerbNounPrepositionNounCommandFromInvalidVerbToken()
    {
        $commandParser = $this->createCommandParser();
        $outputController = new OutputController();
        $commandFactory = new CommandFactory($commandParser, $outputController);
        $this->expectException(InvalidVerbException::class);
        $commandFactory->createFromTokens(['sword', 'sword', 'into', 'sheath']);
    }

    public function testCreateVerbNounPrepositionNounCommandFromInvalidNoun1Token()
    {
        $commandParser = $this->createCommandParser();
        $outputController = new OutputController();
        $commandFactory = new CommandFactory($commandParser, $outputController);
        $this->expectException(InvalidNounException::class);
        $commandFactory->createFromTokens(['put', 'put', 'into', 'sheath']);
    }

    public function testCreateVerbNounPrepositionNounCommandFromInvalidPrepositionToken()
    {
        $commandParser = $this->createCommandParser();
        $outputController = new OutputController();
        $commandFactory = new CommandFactory($commandParser, $outputController);
        $this->expectException(InvalidPrepositionException::class);
        $commandFactory->createFromTokens(['put', 'sword', 'put', 'sheath']);
    }

    public function testCreateVerbNounPrepositionNounCommandFromInvalidNoun2Token()
    {
        $commandParser = $this->createCommandParser();
        $outputController = new OutputController();
        $commandFactory = new CommandFactory($commandParser, $outputController);
        $this->expectException(InvalidNounException::class);
        $commandFactory->createFromTokens(['put', 'sword', 'into', 'put']);
    }

    public function testCreateFromTokensUnsupportedTokensLength()
    {
        $commandParser = $this->createCommandParser();
        $outputController = new OutputController();
        $commandFactory = new CommandFactory($commandParser, $outputController);
        $this->expectException(InvalidTokensLengthException::class);
        $commandFactory->createFromTokens([]);
    }
}
