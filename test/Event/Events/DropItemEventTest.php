<?php

namespace AdventureGame\Test\Event\Events;

use AdventureGame\Event\AbstractInventoryEvent;
use AdventureGame\Event\Events\DropItemEvent;
use AdventureGame\Event\Triggers\DropItemFromInventoryTrigger;
use AdventureGame\Item\Container;
use AdventureGame\Item\Item;
use AdventureGame\Location\Location;
use AdventureGame\Test\Event\AbstractEventTest;
use AdventureGame\Test\FrameworkTest;

class DropItemEventTest extends FrameworkTest
{
    public function testDropItemEventWithSingleUseDropItemFromInventoryTrigger()
    {
        $item = new Item(
            'has-item-id',
            'Test Drop Item',
            'A test item to be dropped',
            ['dropped']
        );

        $location = new Location(
            'test-location-id',
            'Test Location',
            'A test location',
            new Container(),
            []
        );

        $trigger = new DropItemFromInventoryTrigger($item->getId());
        $event = new DropItemEvent(
            $trigger,
            'has-item-id',
            'test-location-id',
        );

        $gameController = $this->createGameController();
        $gameController->mapController->addLocation($location);
        $gameController->mapController->setPlayerLocationById('test-location-id');
        $gameController->playerController->addItemToPlayerInventory($item);

        // Event not added yet.
        $gameController->eventController->processDropItemEvents(
            $gameController,
            'has-item-id'
        );

        $this->assertEquals(
            $item,
            $gameController->playerController->getItemByIdFromPlayerInventory('has-item-id')
        );

        // Works after adding and executing event.
        $gameController->eventController->addEvent($event);

        $gameController->eventController->processDropItemEvents(
            $gameController,
            'has-item-id'
        );

        $this->assertNull(
            $gameController->playerController->getItemByIdFromPlayerInventory('has-item-id')
        );

        $gameController->playerController->addItemToPlayerInventory($item);

        // Number of uses exceeded.
        $gameController->eventController->processDropItemEvents(
            $gameController,
            'has-item-id'
        );

        $this->assertEquals(
            $item,
            $gameController->playerController->getItemByIdFromPlayerInventory('has-item-id')
        );
    }

    public function testDropItemEventWithMultipleUseDropItemFromInventoryTrigger()
    {
        $item = new Item(
            'has-item-id',
            'Test Drop Item',
            'A test item to be dropped',
            ['dropped']
        );

        $location = new Location(
            'test-location-id',
            'Test Location',
            'A test location',
            new Container(),
            []
        );

        $trigger = new DropItemFromInventoryTrigger($item->getId(), 2);
        $event = new DropItemEvent(
            $trigger,
            'has-item-id',
            'test-location-id',
        );

        $gameController = $this->createGameController();
        $gameController->mapController->addLocation($location);
        $gameController->mapController->setPlayerLocationById('test-location-id');
        $gameController->playerController->addItemToPlayerInventory($item);

        // Event not added yet.
        $gameController->eventController->processDropItemEvents(
            $gameController,
            'has-item-id'
        );

        $this->assertEquals(
            $item,
            $gameController->playerController->getItemByIdFromPlayerInventory('has-item-id')
        );

        // Works after adding and executing event.
        $gameController->eventController->addEvent($event);
        $gameController->eventController->processDropItemEvents(
            $gameController,
            'has-item-id'
        );

        $this->assertNull(
            $gameController->playerController->getItemByIdFromPlayerInventory('has-item-id')
        );

        // Works again
        $gameController->playerController->addItemToPlayerInventory($item);
        $gameController->eventController->addEvent($event);
        $gameController->eventController->processDropItemEvents(
            $gameController,
            'has-item-id'
        );

        $this->assertNull(
            $gameController->playerController->getItemByIdFromPlayerInventory('has-item-id')
        );

        // Number of uses exceeded.
        $gameController->playerController->addItemToPlayerInventory($item);
        $gameController->eventController->processDropItemEvents(
            $gameController,
            'has-item-id'
        );

        $this->assertEquals(
            $item,
            $gameController->playerController->getItemByIdFromPlayerInventory('has-item-id')
        );
    }
}
