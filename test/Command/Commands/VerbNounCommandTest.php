<?php

namespace AdventureGame\Test\Command\Commands;

use AdventureGame\Command\Commands\VerbNounCommand;
use AdventureGame\Game\Exception\InvalidExitException;
use AdventureGame\Test\FrameworkTest;

class VerbNounCommandTest extends FrameworkTest
{
    public function testProcessMovePlayer()
    {
        $gameController = $this->createGameController();

        // Player starting room.
        $location = $gameController->mapController->getPlayerLocation();
        $this->assertEquals('test-room-1', $location->getId());

        // Move player east.
        $command = new VerbNounCommand('go', 'east');
        $response = $command->process($gameController);
        $this->assertNotNull($response);
        $location = $gameController->mapController->getPlayerLocation();
        $this->assertEquals('test-room-2', $location->getId());

        // Move player west.
        $command = new VerbNounCommand('go', 'west');
        $response = $command->process($gameController);
        $this->assertNotNull($response);
        $location = $gameController->mapController->getPlayerLocation();
        $this->assertEquals('test-room-1', $location->getId());
    }

    public function testProcessMovePlayerInvalidExit()
    {
        $gameController = $this->createGameController();
        // Player starting room.
        $location = $gameController->mapController->getPlayerLocation();
        $this->assertEquals('test-room-1', $location->getId());

        // Try to move player south.
        $command = new VerbNounCommand('go', 'south');
        $this->expectException(InvalidExitException::class);
        $response = $command->process($gameController);
        $this->assertNotNull($response);
    }

    public function testProcessNoAction()
    {
        $gameController = $this->createGameController();

        $items = $gameController->playerController->getItemsByTagFromPlayerInventory('test');
        $this->assertCount(0, $items);

        $command = new VerbNounCommand('launch', 'test');
        $response = $command->process($gameController);
        $this->assertNull($response);
    }

    public function testProcessTakeItem()
    {
        $gameController = $this->createGameController();
        $itemCount = $gameController->mapController->getItemCount();

        $items = $gameController->playerController->getItemsByTagFromPlayerInventory('test');
        $this->assertCount(0, $items);

        $command = new VerbNounCommand('take', 'test');
        $response = $command->process($gameController);
        $this->assertNotNull($response);
        $items = $gameController->playerController->getItemsByTagFromPlayerInventory('test');
        $this->assertCount(1, $items);
        $this->assertEquals($itemCount - 1, $gameController->mapController->getItemCount());
    }

    public function testProcessTakeThenDropItem()
    {
        $gameController = $this->createGameController();

        $itemCount = $gameController->mapController->getItemCount();

        $items = $gameController->playerController->getItemsByTagFromPlayerInventory('test');
        $this->assertCount(0, $items);

        $command = new VerbNounCommand('take', 'test');
        $response = $command->process($gameController);
        $this->assertNotNull($response);
        $items = $gameController->playerController->getItemsByTagFromPlayerInventory('test');
        $this->assertCount(1, $items);
        $this->assertEquals($itemCount - 1, $gameController->mapController->getItemCount());

        $command = new VerbNounCommand('drop', 'test');
        $response = $command->process($gameController);
        $this->assertNotNull($response);
        $items = $gameController->playerController->getItemsByTagFromPlayerInventory('test');
        $this->assertCount(0, $items);
        $this->assertEquals($itemCount, $gameController->mapController->getItemCount());
    }
}
