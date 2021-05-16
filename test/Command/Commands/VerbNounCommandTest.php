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
        $outputController = $this->createOutputController();

        // Player starting room.
        $location = $gameController->mapController->getPlayerLocation();
        $this->assertEquals('test-room-1', $location->getId());

        // Move player east.
        $command = new VerbNounCommand('go', 'east', $outputController);
        $result = $command->process($gameController);
        $this->assertTrue($result);
        $location = $gameController->mapController->getPlayerLocation();
        $this->assertEquals('test-room-2', $location->getId());

        // Move player west.
        $command = new VerbNounCommand('go', 'west', $outputController);
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
        $command = new VerbNounCommand('go', 'south', $outputController);
        $this->expectException(InvalidExitException::class);
        $result = $command->process($gameController);
        $this->assertTrue($result);
    }

    public function testProcessTakeItem()
    {
        $gameController = $this->createGameController();
        $outputController = $this->createOutputController();

        $itemCount = $gameController->mapController->getItemCount();

        $items = $gameController->playerController->getItemsByTagFromPlayerInventory('test');
        $this->assertCount(0, $items);

        $command = new VerbNounCommand('take', 'test', $outputController);
        $result = $command->process($gameController);
        $this->assertTrue($result);
        $items = $gameController->playerController->getItemsByTagFromPlayerInventory('test');
        $this->assertCount(1, $items);
        $this->assertEquals($itemCount - 1, $gameController->mapController->getItemCount());
    }

    public function testProcessNoAction()
    {
        $gameController = $this->createGameController();
        $outputController = $this->createOutputController();

        $items = $gameController->playerController->getItemsByTagFromPlayerInventory('test');
        $this->assertCount(0, $items);

        $command = new VerbNounCommand('launch', 'test', $outputController);
        $result = $command->process($gameController);
        $this->assertFalse($result);
    }

    public function testProcessTakeThenDropItem()
    {
        $gameController = $this->createGameController();
        $outputController = $this->createOutputController();

        $itemCount = $gameController->mapController->getItemCount();

        $items = $gameController->playerController->getItemsByTagFromPlayerInventory('test');
        $this->assertCount(0, $items);

        $command = new VerbNounCommand('take', 'test', $outputController);
        $result = $command->process($gameController);
        $this->assertTrue($result);
        $items = $gameController->playerController->getItemsByTagFromPlayerInventory('test');
        $this->assertCount(1, $items);
        $this->assertEquals($itemCount - 1, $gameController->mapController->getItemCount());

        $command = new VerbNounCommand('drop', 'test', $outputController);
        $result = $command->process($gameController);
        $this->assertTrue($result);
        $items = $gameController->playerController->getItemsByTagFromPlayerInventory('test');
        $this->assertCount(0, $items);
        $this->assertEquals($itemCount, $gameController->mapController->getItemCount());
    }
}
