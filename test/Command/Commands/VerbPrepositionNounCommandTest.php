<?php

namespace AdventureGame\Test\Command\Commands;

use AdventureGame\Command\Commands\VerbPrepositionNounCommand;
use AdventureGame\Item\Item;

class VerbPrepositionNounCommandTest extends CommandTest
{
    public function testProcessLookAtItem()
    {
        $gameController = $this->createGameController();
        $outputController = $this->createOutputController();

        $command = new VerbPrepositionNounCommand(
            'look', 'at', 'test', $outputController
        );
        $result = $command->process($gameController);
        $this->assertTrue($result);
        $this->assertCount(2, $outputController->getLinesAndClear());

        // Add another item.
        $gameController->mapController->dropItem(
            new Item('test2', 'test-item-2', 'another item', 'test')
        );

        $command = new VerbPrepositionNounCommand(
            'look', 'at', 'test', $outputController
        );
        $result = $command->process($gameController);
        $this->assertTrue($result);
        $this->assertCount(4, $outputController->getLinesAndClear());
    }

    public function testProcessNoAction()
    {
        $gameController = $this->createGameController();
        $outputController = $this->createOutputController();

        $command = new VerbPrepositionNounCommand(
            'north', 'at', 'test', $outputController
        );
        $result = $command->process($gameController);
        $this->assertFalse($result);
    }
}
