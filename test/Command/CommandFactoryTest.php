<?php

namespace AdventureGame\Test\Command;

use AdventureGame\Command\CommandFactory;
use AdventureGame\Command\CommandParser;
use AdventureGame\Command\Commands\VerbCommand;
use AdventureGame\Command\Commands\VerbNounCommand;
use AdventureGame\Command\Commands\VerbNounPrepositionNounCommand;
use AdventureGame\Command\Commands\VerbPrepositionNounCommand;
use AdventureGame\IO\OutputController;
use PHPUnit\Framework\TestCase;

class CommandFactoryTest extends TestCase
{
    private function createCommandParser()
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

    public function testCreateVerbNounCommandFromTokens()
    {
        $commandParser = $this->createCommandParser();
        $outputController = new OutputController();
        $commandFactory = new CommandFactory($commandParser, $outputController);
        $command = $commandFactory->createFromTokens(['take', 'sword']);

        $this->assertInstanceOf(VerbNounCommand::class, $command);
    }

    public function testCreateVerbPrepositionNounCommandFromTokens()
    {
        $commandParser = $this->createCommandParser();
        $outputController = new OutputController();
        $commandFactory = new CommandFactory($commandParser, $outputController);
        $command = $commandFactory->createFromTokens(['look', 'at', 'sword']);

        $this->assertInstanceOf(VerbPrepositionNounCommand::class, $command);
    }

    public function testCreateVerbNounPrepositionNounCommandFromTokens()
    {
        $commandParser = $this->createCommandParser();
        $outputController = new OutputController();
        $commandFactory = new CommandFactory($commandParser, $outputController);
        $command = $commandFactory->createFromTokens(['put', 'sword', 'into', 'sheath']);

        $this->assertInstanceOf(VerbNounPrepositionNounCommand::class, $command);
    }
}
