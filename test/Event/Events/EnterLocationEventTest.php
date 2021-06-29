<?php

namespace AdventureGame\Test\Event\Events;

use AdventureGame\Event\Events\EnterLocationEvent;
use AdventureGame\Item\Container;
use AdventureGame\Location\Location;
use AdventureGame\Response\Response;
use AdventureGame\Test\Event\AbstractEventTest;

class EnterLocationEventTest extends AbstractEventTest
{
    public function testEnterLocationEvent()
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

        $event = new EnterLocationEvent(
            $mockTrigger,
            'test-location-id',
        );

        $gameController = $this->createGameController();
        $gameController->mapController->addLocation($location);
        $gameController->mapController->setPlayerLocationById('test-location-id');
        $gameController->eventController->addEvent($event);

        $this->assertEquals(
            $mockResponse,
            $gameController->eventController->processEnterLocationEvents(
                $gameController,
                'test-location-id'
            )
        );
    }
}