<?php

namespace AdventureGame\Test\Event\Events;

use AdventureGame\Event\Events\ActivateItemEvent;
use AdventureGame\Event\Triggers\AddItemToLocationUseTrigger;
use AdventureGame\Event\Triggers\RemoveItemFromLocationUseTrigger;
use AdventureGame\Item\Container;
use AdventureGame\Item\Item;
use AdventureGame\Location\Location;
use AdventureGame\Response\Response;
use AdventureGame\Test\Event\AbstractEventTest;

class ActivateItemEventTest extends AbstractEventTest
{
    public function testActivateItemEvent()
    {
        $location = new Location(
            'test-location-id',
            'Test Location',
            ['A test location'],
            new Container(),
            []
        );

        $mockResponse = new Response();
        $mockTrigger = $this->createMockTrigger($mockResponse);

        $event = new ActivateItemEvent(
            $mockTrigger,
            'test-item-id',
            'test-location-id',
        );

        $gameController = $this->createGameController();
        $gameController->mapController->addLocation($location);
        $gameController->mapController->setPlayerLocationById('test-location-id');
        $gameController->eventController->addEvent($event);

        $this->assertEquals(
            $mockResponse,
            $gameController->eventController->processActivateItemEvents(
                $gameController,
                'test-item-id'
            )
        );
    }

    public function testActivateItemEventAddItemToLocationEvent()
    {
        $location = new Location(
            'test-location-id',
            'Test Location',
            ['A test location'],
            new Container(),
            []
        );

        $item = new Item('test-item-id', 'test item', [], []);
        $trigger = new AddItemToLocationUseTrigger($item, 1);

        $event = new ActivateItemEvent(
            $trigger,
            'test-item-id',
            'test-location-id',
        );

        $gameController = $this->createGameController();
        $gameController->mapController->addLocation($location);
        $gameController->mapController->setPlayerLocationById('test-location-id');
        $gameController->eventController->addEvent($event);

        $response = $gameController->eventController->processActivateItemEvents(
            $gameController,
            'test-item-id'
        );

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals('Revealed "test item".', $response->getMessages()[0]);
    }

    public function testActivateItemEventRemoveItemFromLocationEvent()
    {
        $container = new Container();
        $item = new Item('test-item-id', 'test item', [], []);
        $container->addItem($item);

        $location = new Location(
            'test-location-id',
            'Test Location',
            ['A test location'],
            $container,
            []
        );

        $trigger = new RemoveItemFromLocationUseTrigger($item, 1);

        $event = new ActivateItemEvent(
            $trigger,
            'test-item-id',
            'test-location-id',
        );

        $gameController = $this->createGameController();
        $gameController->mapController->addLocation($location);
        $gameController->mapController->setPlayerLocationById('test-location-id');
        $gameController->eventController->addEvent($event);

        $this->assertEquals([$item], $container->getItems());

        $response = $gameController->eventController->processActivateItemEvents(
            $gameController,
            'test-item-id'
        );

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals('Removed "test item".', $response->getMessages()[0]);
        $this->assertEquals([], $container->getItems());
    }

    public function testActivateItemEventAddItemAndRemoveItemFromLocationEvent()
    {
        $container = new Container();

        $location = new Location(
            'test-location-id',
            'Test Location',
            ['A test location'],
            $container,
            []
        );

        // Add this item on event.
        $item = new Item('test-item-id', 'test item', [], []);
        $trigger = new AddItemToLocationUseTrigger($item, 1);
        $event = new ActivateItemEvent(
            $trigger,
            'test-item-id',
            'test-location-id',
        );

        // Remove this item on event.
        $item2 = new Item('test-item-id-2', 'test item 2', [], []);
        $trigger2 = new RemoveItemFromLocationUseTrigger($item2, 1);
        $event2 = new ActivateItemEvent(
            $trigger2,
            'test-item-id',
            'test-location-id',
        );
        $container->addItem($item2);

        $gameController = $this->createGameController();
        $gameController->mapController->addLocation($location);
        $gameController->mapController->setPlayerLocationById('test-location-id');
        $gameController->eventController->addEvent($event);
        $gameController->eventController->addEvent($event2);

        $response = $gameController->eventController->processActivateItemEvents(
            $gameController,
            'test-item-id'
        );

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals('Revealed "test item".', $response->getMessages()[0]);
        $this->assertEquals('Removed "test item 2".', $response->getMessages()[1]);
    }
}
