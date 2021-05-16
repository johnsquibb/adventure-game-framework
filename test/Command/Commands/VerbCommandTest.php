<?php

namespace AdventureGame\Test\Command\Commands;

use AdventureGame\Command\Commands\VerbCommand;
use AdventureGame\Game\Exception\InvalidExitException;
use AdventureGame\Test\FrameworkTest;

class VerbCommandTest extends FrameworkTest
{
    public function testProcessLook()
    {
        $gameController = $this->createGameController();
        $outputController = $this->createOutputController();

        // Player starting room.
        $location = $gameController->mapController->getPlayerLocation();
        $this->assertEquals('test-room-1', $location->getId());

        // Describe current room.
        $command = new VerbCommand('look', $outputController);
        $result = $command->process($gameController);
        $this->assertTrue($result);
        $location = $gameController->mapController->getPlayerLocation();
        $this->assertEquals('test-room-1', $location->getId());

        $this->assertCount(9, $outputController->getLines());
    }

    public function testProcessNoAction()
    {
        $gameController = $this->createGameController();
        $outputController = $this->createOutputController();

        // Player starting room.
        $location = $gameController->mapController->getPlayerLocation();
        $this->assertEquals('test-room-1', $location->getId());

        // Describe current room.
        $command = new VerbCommand('run', $outputController);
        $result = $command->process($gameController);
        $this->assertFalse($result);
    }
}
