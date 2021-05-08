<?php

namespace AdventureGame\Test\Command\Commands;

use AdventureGame\Command\Commands\VerbNounCommand;

class VerbNounCommandTest extends CommandTest
{
    public function testProcessTakeItem()
    {
        $gameController = $this->createGameController();
        $outputController = $this->createOutputController();

        $items = $gameController->playerController->getItemsByTagFromPlayerInventory('test');
        $this->assertCount(0, $items);

        $command = new VerbNounCommand('take', 'test', $outputController);
        $command->process($gameController);
        $items = $gameController->playerController->getItemsByTagFromPlayerInventory('test');
        $this->assertCount(1, $items);
    }

    public function testProcessTakeThenDropItem()
    {
        $gameController = $this->createGameController();
        $outputController = $this->createOutputController();

        $items = $gameController->playerController->getItemsByTagFromPlayerInventory('test');
        $this->assertCount(0, $items);

        $command = new VerbNounCommand('take', 'test', $outputController);
        $command->process($gameController);
        $items = $gameController->playerController->getItemsByTagFromPlayerInventory('test');
        $this->assertCount(1, $items);

        $command = new VerbNounCommand('drop', 'test', $outputController);
        $command->process($gameController);
        $items = $gameController->playerController->getItemsByTagFromPlayerInventory('test');
        $this->assertCount(0, $items);
    }
}
