<?php

namespace AdventureGame\Test\Game;

use AdventureGame\Command\Exception\InvalidTokenException;
use AdventureGame\Command\Exception\InvalidTokensLengthException;
use AdventureGame\Test\FrameworkTest;

class CommandControllerTest extends FrameworkTest
{
    public function testCreateAndProcessCommandFromInvalidTokens()
    {
        $commandController = $this->createCommandController();

        $this->expectException(InvalidTokenException::class);
        $commandController->createAndProcessCommandFromTokens(['beep', 'beep', '...']);
    }

    public function testCreateAndProcessCommandFromInvalidTokensLength()
    {
        $commandController = $this->createCommandController();

        $this->expectException(InvalidTokensLengthException::class);
        $commandController->createAndProcessCommandFromTokens([]);
    }

    public function testCreateAndProcessCommandFromTokens()
    {
        $commandController = $this->createCommandController();

        $response = $commandController->createAndProcessCommandFromTokens(
            ['take', 'item', 'from', 'container']
        );

        $this->assertNotNull($response);
    }
}