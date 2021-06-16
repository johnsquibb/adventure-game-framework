<?php

namespace AdventureGame\Test\Event\Events;

use AdventureGame\Event\Events\ActivateItemEvent;
use AdventureGame\Item\Container;
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

        $this->assertSame(
            $mockResponse,
            $gameController->eventController->processActivateItemEvents(
                $gameController,
                'test-item-id'
            )
        );
    }
}
