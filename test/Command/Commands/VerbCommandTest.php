<?php

namespace AdventureGame\Test\Command\Commands;

use AdventureGame\Command\Commands\VerbCommand;
use AdventureGame\Game\Exception\InvalidExitException;
use AdventureGame\Test\FrameworkTest;

class VerbCommandTest extends FrameworkTest
{
    public function testProcessMovePlayer()
    {
        $gameController = $this->createGameController();
        $outputController = $this->createOutputController();

        // Player starting room.
        $location = $gameController->mapController->getPlayerLocation();
        $this->assertEquals('test-room-1', $location->getId());

        // Move player east.
        $command = new VerbCommand('east', $outputController);
        $result = $command->process($gameController);
        $this->assertTrue($result);
        $location = $gameController->mapController->getPlayerLocation();
        $this->assertEquals('test-room-2', $location->getId());

        // Move player west.
        $command = new VerbCommand('west', $outputController);
        $result = $command->process($gameController);
        $this->assertTrue($result);
        $location = $gameController->mapController->getPlayerLocation();
        $this->assertEquals('test-room-1', $location->getId());
    }

    public function testProcessMovePlayerInvalidExit()
    {
        $gameController = $this->createGameController();
        $outputController = $this->createOutputController();

        // Player starting room.
        $location = $gameController->mapController->getPlayerLocation();
        $this->assertEquals('test-room-1', $location->getId());

        // Try to move player south.
        $command = new VerbCommand('south', $outputController);
        $this->expectException(InvalidExitException::class);
        $result = $command->process($gameController);
        $this->assertTrue($result);
    }

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
