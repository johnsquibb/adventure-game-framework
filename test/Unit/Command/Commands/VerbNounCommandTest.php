<?php

namespace AdventureGame\Command\Commands;

use AdventureGame\Game\MapController;
use AdventureGame\Item\Container;
use AdventureGame\Location\Location;
use PHPUnit\Framework\TestCase;

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
