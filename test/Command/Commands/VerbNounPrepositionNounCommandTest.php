<?php

namespace AdventureGame\Test\Command\Commands;

use AdventureGame\Command\Commands\VerbNounPrepositionNounCommand;
use AdventureGame\Item\ContainerInterface;

class VerbNounPrepositionNounCommandTest extends CommandTest
{
    public function testProcessTakeItemFromContainer()
    {
        $gameController = $this->createGameController();
        $outputController = $this->createOutputController();

        $items = $gameController->playerController->getItemsByTagFromPlayerInventory('test');
        $this->assertCount(0, $items);

        $command = new VerbNounPrepositionNounCommand(
            'take',
            'test-item-in-container',
            'from',
            'test-container-item',
            $outputController
        );
        $command->process($gameController);
        $items = $gameController->playerController->getItemsByTagFromPlayerInventory(
            'test-item-in-container'
        );
        $this->assertCount(1, $items);
    }

    public function testProcessTakeItemFromContainerThenDropItemIntoContainer()
    {
        $gameController = $this->createGameController();
        $outputController = $this->createOutputController();

        /** @var ContainerInterface $container */
        $container = $gameController->mapController->getPlayerLocation()->items
            ->getItemById('test-container-item');
        $itemCount = $container->countItems();

        $items = $gameController->playerController->getItemsByTagFromPlayerInventory(
            'test-item-in-container'
        );
        $this->assertCount(0, $items);

        $command = new VerbNounPrepositionNounCommand(
            'take',
            'test-item-in-container',
            'from',
            'test-container-item',
            $outputController
        );
        $command->process($gameController);
        $items = $gameController->playerController->getItemsByTagFromPlayerInventory(
            'test-item-in-container'
        );
        $this->assertCount(1, $items);
        $this->assertEquals($itemCount - 1, $container->countItems());

        $command = new VerbNounPrepositionNounCommand(
            'drop',
            'test-item-in-container',
            'into',
            'test-container-item',
            $outputController
        );
        $command->process($gameController);
        $items = $gameController->playerController->getItemsByTagFromPlayerInventory(
            'test-item-in-container'
        );
        $this->assertCount(0, $items);
        $this->assertEquals($itemCount, $container->countItems());
    }
}
