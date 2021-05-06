<?php

namespace AdventureGame\Game;

use AdventureGame\Game\Exception\InvalidExitException;
use AdventureGame\Game\Exception\PlayerLocationNotSetException;
use AdventureGame\Item\Container;
use AdventureGame\Location\Location;
use AdventureGame\Location\Portal;
use PHPUnit\Framework\TestCase;

class MapControllerTest extends TestCase
{
    public function testCreateMapController()
    {
        $locations = [];

        $mapController = new MapController($locations);
        $this->assertNull($mapController->getPlayerLocation());
    }

    public function testSetPlayerLocation()
    {
        $location = new Location('test-location', 'Test Location', new Container(), []);
        $locations = [$location];

        $mapController = new MapController($locations);
        $mapController->setPlayerLocationById($location->id);
        $this->assertEquals($location, $mapController->getPlayerLocation());
    }

    public function testMovePlayerPlayerLocationNotSet()
    {
        $location = new Location('test-location', 'Test Location', new Container(), []);
        $locations = [$location];

        $mapController = new MapController($locations);
        $this->expectException(PlayerLocationNotSetException::class);
        $mapController->movePlayer('anywhere');
    }

    public function testMovePlayer()
    {
        $door1 = new Portal('test-door', 'east', 'test-room-2');
        $location1 = new Location(
            'test-room-1',
            'Test Room 1',
            new Container(),
            [$door1],
        );

        $door2 = new Portal('test-door', 'west', 'test-room-1');
        $location2 = new Location(
            'test-room-2',
            'Test Room 2',
            new Container(),
            [$door2],
        );

        $locations = [$location1, $location2];

        // Spawn player in room 1
        $mapController = new MapController($locations);
        $mapController->setPlayerLocationById($location1->id);
        $this->assertEquals($location1, $mapController->getPlayerLocation());

        // Move to room 2
        $mapController->movePlayer($door1->direction);
        $this->assertEquals($location2, $mapController->getPlayerLocation());

        // Move back to room 1
        $mapController->movePlayer($door2->direction);
        $this->assertEquals($location1, $mapController->getPlayerLocation());

        // Try to move to nonexistent room.
        $this->expectException(InvalidExitException::class);
        $mapController->movePlayer('nowhere');
    }
}
