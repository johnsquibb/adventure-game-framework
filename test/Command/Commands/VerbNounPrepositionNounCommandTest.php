<?php

namespace AdventureGame\Test\Command\Commands;

use AdventureGame\Command\Commands\VerbNounPrepositionNounCommand;
use AdventureGame\Item\ContainerInterface;
use AdventureGame\Test\FrameworkTest;

class VerbNounPrepositionNounCommandTest extends FrameworkTest
{
    public function testProcessNoAction()
    {
        $gameController = $this->createGameController();

        $items = $gameController->playerController->getItemsByTagFromPlayerInventory('test');
        $this->assertCount(0, $items);

        $command = new VerbNounPrepositionNounCommand(
            'fly',
            'test-item-in-container',
            'from',
            'test-container-item'
        );
        $response = $command->process($gameController);
        $this->assertNull($response);
    }

    public function testProcessTakeItemFromContainer()
    {
        $gameController = $this->createGameController();

        $items = $gameController->playerController->getItemsByTagFromPlayerInventory('test');
        $this->assertCount(0, $items);

        $command = new VerbNounPrepositionNounCommand(
            'take',
            'test-item-in-container',
            'from',
            'test-container-item'
        );
        $response = $command->process($gameController);
        $this->assertNotNull($response);
        $items = $gameController->playerController->getItemsByTagFromPlayerInventory(
            'test-item-in-container'
        );
        $this->assertCount(1, $items);
    }

    public function testProcessTakeItemFromContainerThenDropItemIntoContainer()
    {
        $gameController = $this->createGameController();

        /** @var ContainerInterface $container */
        $container = $gameController->mapController->getPlayerLocation()->getContainer()
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
            'test-container-item'
        );
        $response = $command->process($gameController);
        $this->assertNotNull($response);
        $items = $gameController->playerController->getItemsByTagFromPlayerInventory(
            'test-item-in-container'
        );
        $this->assertCount(1, $items);
        $this->assertEquals($itemCount - 1, $container->countItems());

        $command = new VerbNounPrepositionNounCommand(
            'drop',
            'test-item-in-container',
            'into',
            'test-container-item'
        );
        $response = $command->process($gameController);
        $this->assertNotNull($response);
        $items = $gameController->playerController->getItemsByTagFromPlayerInventory(
            'test-item-in-container'
        );
        $this->assertCount(0, $items);
        $this->assertEquals($itemCount, $container->countItems());
    }
}
