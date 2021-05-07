<?php

namespace AdventureGame\Command\Commands;

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
        $command->process($gameController);
        $location = $gameController->mapController->getPlayerLocation();
        $this->assertEquals('test-room-2', $location->id);

        // Move player west.
        $command = new VerbCommand('west', $outputController);
        $command->process($gameController);
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
        $command->process($gameController);
    }
}
