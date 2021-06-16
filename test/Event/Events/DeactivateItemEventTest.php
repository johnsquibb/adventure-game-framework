<?php

namespace AdventureGame\Test\Event\Events;

use AdventureGame\Event\Events\ActivateItemEvent;
use AdventureGame\Event\Events\DeactivateItemEvent;
use AdventureGame\Item\Container;
use AdventureGame\Location\Location;
use AdventureGame\Response\Response;
use AdventureGame\Test\Event\AbstractEventTest;

class DeactivateItemEventTest extends AbstractEventTest
{
    public function testDeactivateItemEvent()
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

        $event = new DeactivateItemEvent(
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
            $gameController->eventController->processDeactivateItemEvents(
                $gameController,
                'test-item-id'
            )
        );
    }
}
