<?php

namespace AdventureGame\Test\Command\Commands;

use AdventureGame\Command\Commands\VerbCommand;
use AdventureGame\Game\Exception\InvalidExitException;

class VerbCommandTest extends CommandTest
{
    public function testProcessMovePlayer()
    {
        $gameController = $this->createGameController();
        $outputController = $this->createOutputController();

        // Player starting room.
        $location = $gameController->mapController->getPlayerLocation();
        $this->assertEquals('test-room-1', $location->id);

        // Move player east.
        $command = new VerbCommand('east', $outputController);
        $result = $command->process($gameController);
        $this->assertTrue($result);
        $location = $gameController->mapController->getPlayerLocation();
        $this->assertEquals('test-room-2', $location->id);

        // Move player west.
        $command = new VerbCommand('west', $outputController);
        $result = $command->process($gameController);
        $this->assertTrue($result);
        $location = $gameController->mapController->getPlayerLocation();
        $this->assertEquals('test-room-1', $location->id);
    }

    public function testProcessMovePlayerInvalidExit()
    {
        $gameController = $this->createGameController();
        $outputController = $this->createOutputController();

        // Player starting room.
        $location = $gameController->mapController->getPlayerLocation();
        $this->assertEquals('test-room-1', $location->id);

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
        $this->assertEquals('test-room-1', $location->id);

        // Describe current room.
        $command = new VerbCommand('look', $outputController);
        $result = $command->process($gameController);
        $this->assertTrue($result);
        $location = $gameController->mapController->getPlayerLocation();
        $this->assertEquals('test-room-1', $location->id);

        $this->assertCount(2, $outputController->getLines());
    }

    public function testProcessNoAction()
    {
        $gameController = $this->createGameController();
        $outputController = $this->createOutputController();

        // Player starting room.
        $location = $gameController->mapController->getPlayerLocation();
        $this->assertEquals('test-room-1', $location->id);

        // Describe current room.
        $command = new VerbCommand('run', $outputController);
        $result = $command->process($gameController);
        $this->assertFalse($result);
    }
}
