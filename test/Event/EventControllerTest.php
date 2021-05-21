<?php

namespace AdventureGame\Test\Event;

use AdventureGame\Event\Events\TakeItemEvent;
use AdventureGame\Event\Triggers\AddItemToInventoryTrigger;
use AdventureGame\Item\Container;
use AdventureGame\Item\Item;
use AdventureGame\Location\Location;
use AdventureGame\Test\FrameworkTest;

class EventControllerTest extends FrameworkTest
{
    public function testAddEvent()
    {
        $item = new Item(
            'test-give-item-id',
            'Test Give Item',
            'A test item given to player on event trigger',
            'given'
        );

        $location = new Location(
            'test-location-id',
            'Test Location',
            'A test location',
            new Container(),
            []
        );

        $trigger = new AddItemToInventoryTrigger($item);
        $event = new TakeItemEvent(
            $trigger,
            'test-item-id',
            'test-location-id',
        );

        $gameController = $this->createGameController();
        $gameController->mapController->addLocation($location);
        $gameController->mapController->setPlayerLocationById('test-location-id');

        $this->assertNull(
            $gameController->playerController->getItemByIdFromPlayerInventory('test-give-item-id')
        );

        // Event not added yet.
        $gameController->eventController->processInventoryTakeEvents(
            $gameController,
            'test-item-id'
        );

        $this->assertNull(
            $gameController->playerController->getItemByIdFromPlayerInventory('test-give-item-id')
        );

        $gameController->eventController->addEvent($event);

        $gameController->eventController->processInventoryTakeEvents(
            $gameController,
            'test-item-id'
        );

        $this->assertEquals(
            $item,
            $gameController->playerController->getItemByIdFromPlayerInventory('test-give-item-id')
        );
    }
}
