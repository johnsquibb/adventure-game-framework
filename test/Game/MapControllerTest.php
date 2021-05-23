<?php

namespace AdventureGame\Test\Game;

use AdventureGame\Game\Exception\InvalidExitException;
use AdventureGame\Game\Exception\PlayerLocationNotSetException;
use AdventureGame\Game\MapController;
use AdventureGame\Item\Container;
use AdventureGame\Item\Item;
use AdventureGame\Location\Location;
use AdventureGame\Location\Portal;
use PHPUnit\Framework\TestCase;

class MapControllerTest extends TestCase
{
    public function testCreateMapController()
    {
        $locations = [];

        $mapController = new MapController($locations);
        $this->expectException(PlayerLocationNotSetException::class);
        $this->assertNull($mapController->getPlayerLocation());
    }

    public function testDropItem()
    {
        $inventory = new Container();
        $item = new Item(
            'test-item',
            'Test Item',
            'Test Item Description',
            ['test']
        );
        $inventory->addItem($item);

        $location = new Location(
            'test-location',
            'Test Location',
            'Test Location Description',
            new Container(),
            []
        );
        $locations = [$location];

        $mapController = new MapController($locations);
        $mapController->setPlayerLocationById($location->getId());
        $this->assertNull($mapController->takeItemById($item->getId()));
        $mapController->dropItem($item);
        $this->assertEquals($item, $mapController->takeItemById($item->getId()));
    }

    public function testFindItemsByTag()
    {
        $items = new Container();
        $item = new Item(
            'test-item-1',
            'Test Item',
            'Test Item Description',
            ['test']
        );

        $location = new Location(
            'test-location',
            'Test Location',
            'Test Location Description',
            $items,
            []
        );
        $locations = [$location];

        $mapController = new MapController($locations);
        $mapController->setPlayerLocationById($location->getId());
        $this->assertEmpty($mapController->takeItemsByTag('test'));

        $location->getContainer()->addItem($item);
        $this->assertCount(1, $mapController->takeItemsByTag('test'));

        $item2 = new Item(
            'test-item-2',
            'Test Item',
            'Test Item Description',
            ['test']
        );

        $location->getContainer()->addItem($item2);
        $this->assertCount(1, $mapController->takeItemsByTag('test'));
        $this->assertCount(0, $mapController->takeItemsByTag('test'));
    }

    public function testMovePlayer()
    {
        $door1 = new Portal(
            'test-door',
            '',
            '',
            ['east'],
            'east', 'test-room-2'
        );
        $location1 = new Location(
            'test-room-1',
            'Test Room 1',
            'This is a test room.',
            new Container(),
            [$door1],
        );

        $door2 = new Portal(
            'test-door',
            '',
            '',
            ['west'],
            'west', 'test-room-1'
        );
        $location2 = new Location(
            'test-room-2',
            'Test Room 2',
            'This is another test room.',
            new Container(),
            [$door2],
        );

        $locations = [$location1, $location2];

        // Spawn player in room 1
        $mapController = new MapController($locations);
        $mapController->setPlayerLocationById($location1->getId());
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

    public function testMovePlayerPlayerLocationNotSet()
    {
        $location = new Location(
            'test-location',
            'Test Location',
            'Test Location Description',
            new Container(),
            []
        );
        $locations = [$location];

        $mapController = new MapController($locations);
        $this->expectException(PlayerLocationNotSetException::class);
        $mapController->movePlayer('anywhere');
    }

    public function testSetPlayerLocation()
    {
        $location = new Location(
            'test-location',
            'Test Location',
            'Test Location Description',
            new Container(),
            []
        );
        $locations = [$location];

        $mapController = new MapController($locations);
        $mapController->setPlayerLocationById($location->getId());
        $this->assertEquals($location, $mapController->getPlayerLocation());
    }

    public function testTakeItem()
    {
        $items = new Container();
        $item = new Item(
            'test-item',
            'Test Item',
            'Test Item Description',
            ['test']
        );
        $items->addItem($item);

        $location = new Location(
            'test-location',
            'Test Location',
            'Test Location Description',
            $items,
            []
        );
        $locations = [$location];

        $mapController = new MapController($locations);
        $mapController->setPlayerLocationById($location->getId());
        $this->assertEquals($item, $mapController->takeItemById($item->getId()));
        $this->assertNull($mapController->takeItemById($item->getId()));
    }
}
