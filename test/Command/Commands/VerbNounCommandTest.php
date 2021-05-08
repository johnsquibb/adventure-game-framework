<?php

namespace AdventureGame\Test\Command\Commands;

use AdventureGame\Command\Commands\VerbNounCommand;

class VerbNounCommandTest extends CommandTest
{
    public function testProcessTakeItem()
    {
        $gameController = $this->createGameController();
        $outputController = $this->createOutputController();

        $itemCount = $gameController->mapController->getItemCount();

        $items = $gameController->playerController->getItemsByTagFromPlayerInventory('test');
        $this->assertCount(0, $items);

        $command = new VerbNounCommand('take', 'test', $outputController);
        $command->process($gameController);
        $items = $gameController->playerController->getItemsByTagFromPlayerInventory('test');
        $this->assertCount(1, $items);
        $this->assertEquals($itemCount - 1, $gameController->mapController->getItemCount());
    }

    public function testProcessTakeThenDropItem()
    {
        $gameController = $this->createGameController();
        $outputController = $this->createOutputController();

        $itemCount = $gameController->mapController->getItemCount();

        $items = $gameController->playerController->getItemsByTagFromPlayerInventory('test');
        $this->assertCount(0, $items);

        $command = new VerbNounCommand('take', 'test', $outputController);
        $command->process($gameController);
        $items = $gameController->playerController->getItemsByTagFromPlayerInventory('test');
        $this->assertCount(1, $items);
        $this->assertEquals($itemCount - 1, $gameController->mapController->getItemCount());

        $command = new VerbNounCommand('drop', 'test', $outputController);
        $command->process($gameController);
        $items = $gameController->playerController->getItemsByTagFromPlayerInventory('test');
        $this->assertCount(0, $items);
        $this->assertEquals($itemCount, $gameController->mapController->getItemCount());
    }
}
