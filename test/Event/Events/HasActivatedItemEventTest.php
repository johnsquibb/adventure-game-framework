<?php

namespace AdventureGame\Test\Event\Events;

use AdventureGame\Event\Events\HasActivatedItemEvent;
use AdventureGame\Item\Container;
use AdventureGame\Location\Location;
use AdventureGame\Response\Response;
use AdventureGame\Test\Event\AbstractEventTest;

class HasActivatedItemEventTest extends AbstractEventTest
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

        $event = new HasActivatedItemEvent(
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
            $gameController->eventController->processHasActivatedItemEvents(
                $gameController,
                'test-item-id'
            )
        );
    }
}
