<?php

namespace AdventureGame\Test\Game;

use AdventureGame\Command\CommandFactory;
use AdventureGame\Command\Exception\InvalidTokenException;
use AdventureGame\Command\Exception\InvalidTokensLengthException;
use AdventureGame\Game\CommandController;
use AdventureGame\Test\FrameworkTest;

class CommandControllerTest extends FrameworkTest
{
    public function testCreateAndProcessCommandFromInvalidTokensLength()
    {
        $gameController = $this->createGameController();
        $commandParser = $this->createCommandParser();
        $outputController = $this->createOutputController();
        $commandFactory = new CommandFactory($commandParser, $outputController);
        $commandController = new CommandController($commandFactory, $gameController);

        $this->expectException(InvalidTokensLengthException::class);
        $commandController->createAndProcessCommandFromTokens([]);
    }

    public function testCreateAndProcessCommandFromInvalidTokens()
    {
        $gameController = $this->createGameController();
        $commandParser = $this->createCommandParser();
        $outputController = $this->createOutputController();
        $commandFactory = new CommandFactory($commandParser, $outputController);
        $commandController = new CommandController($commandFactory, $gameController);

        $this->expectException(InvalidTokenException::class);
        $commandController->createAndProcessCommandFromTokens(['beep', 'beep', '...']);
    }

    public function testCreateAndProcessCommandFromTokens()
    {
        $gameController = $this->createGameController();
        $commandParser = $this->createCommandParser();
        $outputController = $this->createOutputController();
        $commandFactory = new CommandFactory($commandParser, $outputController);
        $commandController = new CommandController($commandFactory, $gameController);

        $result = $commandController->createAndProcessCommandFromTokens(
            ['take', 'sword', 'from', 'chest']
        );

        $this->assertTrue($result);
    }
}
