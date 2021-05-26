<?php

namespace AdventureGame\Test\Event\Events;

use AdventureGame\Event\Events\TakeItemEvent;
use AdventureGame\Event\Triggers\AddItemToInventoryUseTrigger;
use AdventureGame\Item\Container;
use AdventureGame\Item\Item;
use AdventureGame\Location\Location;
use AdventureGame\Response\Response;
use AdventureGame\Test\Event\AbstractEventTest;

class TakeItemEventTest extends AbstractEventTest
{
    public function testTakeItemEvent()
    {
        $location = new Location(
            'test-location-id',
            'Test Location',
            'A test location',
            new Container(),
            []
        );

        $mockResponse = new Response();
        $mockTrigger = $this->createMockTrigger($mockResponse);

        $event = new TakeItemEvent(
            $mockTrigger,
            'test-item-id',
            'test-location-id',
        );

        $gameController = $this->createGameController();
        $gameController->mapController->addLocation($location);
        $gameController->mapController->setPlayerLocationById('test-location-id');
        $gameController->eventController->addEvent($event);

        $this->assertSame(
            $mockResponse,
            $gameController->eventController->processTakeItemEvents(
                $gameController,
                'test-item-id'
            )
        );
    }

    public function testTakeItemEventWithSingleUseAddItemToInventoryTrigger()
    {
        $item = new Item(
            'test-item-id',
            'Test Give Item',
            'A test item given to player on event trigger',
            ['given']
        );

        $location = new Location(
            'test-location-id',
            'Test Location',
            'A test location',
            new Container(),
            []
        );

        $trigger = new AddItemToInventoryUseTrigger($item);
        $event = new TakeItemEvent(
            $trigger,
            '*',
            'test-location-id',
        );

        $gameController = $this->createGameController();
        $gameController->mapController->addLocation($location);
        $gameController->mapController->setPlayerLocationById('test-location-id');

        $this->assertNull(
            $gameController->playerController->getItemByIdFromPlayerInventory('test-item-id')
        );

        // Event not added yet.
        $gameController->eventController->processTakeItemEvents(
            $gameController,
            '*'
        );

        $this->assertNull(
            $gameController->playerController->getItemByIdFromPlayerInventory('test-item-id')
        );

        $gameController->eventController->addEvent($event);

        $gameController->eventController->processTakeItemEvents(
            $gameController,
            '*'
        );

        $this->assertEquals(
            $item,
            $gameController->playerController->getItemByIdFromPlayerInventory('test-item-id')
        );

        // Number of uses exceeded.
        $gameController->eventController->processTakeItemEvents(
            $gameController,
            '*'
        );

        $this->assertEquals(
            $item,
            $gameController->playerController->getItemByIdFromPlayerInventory('test-item-id')
        );
    }

    public function testTakeItemEventWithMultipleUseAddItemToInventoryTrigger()
    {
        $item = new Item(
            'test-item-id',
            'Test Give Item',
            'A test item given to player on event trigger',
            ['given']
        );

        $location = new Location(
            'test-location-id',
            'Test Location',
            'A test location',
            new Container(),
            []
        );

        $trigger = new AddItemToInventoryUseTrigger($item, 3);
        $event = new TakeItemEvent(
            $trigger,
            '*',
            'test-location-id',
        );

        $gameController = $this->createGameController();
        $gameController->mapController->addLocation($location);
        $gameController->mapController->setPlayerLocationById('test-location-id');

        $this->assertNull(
            $gameController->playerController->getItemByIdFromPlayerInventory('test-item-id')
        );

        // Event not added yet.
        $gameController->eventController->processTakeItemEvents(
            $gameController,
            '*'
        );

        $this->assertNull(
            $gameController->playerController->getItemByIdFromPlayerInventory('test-item-id')
        );

        $gameController->eventController->addEvent($event);

        $gameController->eventController->processTakeItemEvents(
            $gameController,
            '*'
        );

        $this->assertEquals(
            $item,
            $gameController->playerController->getItemByIdFromPlayerInventory('test-item-id')
        );

        $this->assertCount(
            1,
            $gameController->playerController->getItemsByTagFromPlayerInventory(
                'given'
            )
        );

        // Number of uses not yet exceeded.
        $gameController->eventController->processTakeItemEvents(
            $gameController,
            '*'
        );

        $this->assertEquals(
            $item,
            $gameController->playerController->getItemByIdFromPlayerInventory('test-item-id')
        );

        $this->assertCount(
            2,
            $gameController->playerController->getItemsByTagFromPlayerInventory(
                'given'
            )
        );

        // Number of uses not yet exceeded.
        $gameController->eventController->processTakeItemEvents(
            $gameController,
            '*'
        );

        $this->assertEquals(
            $item,
            $gameController->playerController->getItemByIdFromPlayerInventory('test-item-id')
        );

        $this->assertCount(
            3,
            $gameController->playerController->getItemsByTagFromPlayerInventory(
                'given'
            )
        );

        // Number of uses exceeded.
        $gameController->eventController->processTakeItemEvents(
            $gameController,
            '*'
        );

        $this->assertEquals(
            $item,
            $gameController->playerController->getItemByIdFromPlayerInventory('test-item-id')
        );

        $this->assertCount(
            3,
            $gameController->playerController->getItemsByTagFromPlayerInventory(
                'given'
            )
        );
    }
}
