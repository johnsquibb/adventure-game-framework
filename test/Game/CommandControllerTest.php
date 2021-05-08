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
        $commandController = $this->createCommandController();

        $this->expectException(InvalidTokensLengthException::class);
        $commandController->createAndProcessCommandFromTokens([]);
    }

    public function testCreateAndProcessCommandFromInvalidTokens()
    {
        $commandController = $this->createCommandController();

        $this->expectException(InvalidTokenException::class);
        $commandController->createAndProcessCommandFromTokens(['beep', 'beep', '...']);
    }

    public function testCreateAndProcessCommandFromTokens()
    {
        $commandController = $this->createCommandController();

        $result = $commandController->createAndProcessCommandFromTokens(
            ['take', 'sword', 'from', 'chest']
        );

        $this->assertTrue($result);
    }
}
