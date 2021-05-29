<?php

namespace AdventureGame\Test\Command\Commands;

use AdventureGame\Command\Commands\VerbPrepositionNounCommand;
use AdventureGame\Item\Item;
use AdventureGame\Test\FrameworkTest;

class VerbPrepositionNounCommandTest extends FrameworkTest
{
    public function testProcessLookAtItem()
    {
        $gameController = $this->createGameController();

        $command = new VerbPrepositionNounCommand(
            'examine', 'at', 'test'
        );
        $response = $command->process($gameController);
        $this->assertNotNull($response);

        // Add another item.
        $gameController->mapController->dropItem(
            new Item('test2', 'test-item-2', 'another item', ['test'])
        );

        $command = new VerbPrepositionNounCommand('examine', 'at', 'test');
        $response = $command->process($gameController);
        $this->assertNotNull($response);
    }

    public function testProcessNoAction()
    {
        $gameController = $this->createGameController();

        $command = new VerbPrepositionNounCommand('north', 'at', 'test');
        $response = $command->process($gameController);
        $this->assertNull($response);
    }
}
