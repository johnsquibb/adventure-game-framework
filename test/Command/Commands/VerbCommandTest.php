<?php

namespace AdventureGame\Test\Command\Commands;

use AdventureGame\Command\Commands\VerbCommand;
use AdventureGame\Test\FrameworkTest;

class VerbCommandTest extends FrameworkTest
{
    public function testProcessLook()
    {
        $gameController = $this->createGameController();

        // Player starting room.
        $location = $gameController->mapController->getPlayerLocation();
        $this->assertEquals('test-room-1', $location->getId());

        // Describe current room.
        $command = new VerbCommand('look');
        $response = $command->process($gameController);
        $this->assertNotNull($response);
        $location = $gameController->mapController->getPlayerLocation();
        $this->assertEquals('test-room-1', $location->getId());
    }

    public function testProcessNoAction()
    {
        $gameController = $this->createGameController();

        // Player starting room.
        $location = $gameController->mapController->getPlayerLocation();
        $this->assertEquals('test-room-1', $location->getId());

        // Describe current room.
        $command = new VerbCommand('run');
        $response = $command->process($gameController);
        $this->assertNull($response);
    }
}
