<?php

namespace AdventureGame\Test\Location;

use AdventureGame\Item\Container;
use AdventureGame\Item\Item;
use AdventureGame\Location\Location;
use AdventureGame\Location\Portal;
use PHPUnit\Framework\TestCase;

class LocationTest extends TestCase
{
    public function testLocationCreate()
    {
        $id = 'test-room';
        $name = 'The Test Room';
        $description = 'This is a test room';
        $items = new Container();

        $room = new Location($id, $name, [$description], $items, []);
        $this->assertEquals($id, $room->getId());
        $this->assertEquals($name, $room->getName());
        $this->assertEquals([$description], $room->getDescription());
        $this->assertEquals($items, $room->getContainer());
    }

    public function testLocationExits()
    {
        $id = 'test-room';
        $name = 'The Test Room';
        $description = 'This is a test room';
        $items = new Container();

        $portal = new Portal(
            'test-door',
            '',
            [],
            ['north'],
            'north',
            'test-room'
        );
        $exits = [$portal];

        $room = new Location($id, $name, [$description], $items, $exits);
        $this->assertEquals($id, $room->getId());
        $this->assertEquals($name, $room->getName());

        $this->assertEquals($portal, $room->getExitInDirection($portal->direction));
        $this->assertNull($room->getExitInDirection('nowhere'));
    }

    public function testLocationItems()
    {
        $id = 'test-room';
        $name = 'The Test Room';
        $description = 'This is a test room';
        $items = new Container();
        $item = new Item(
            'test-item',
            'Test Item',
            ['Test Item Description'],
            ['test']
        );
        $items->addItem($item);

        $room = new Location($id, $name, [$description], $items, []);
        $this->assertEquals($item, $room->getContainer()->getItemById($item->getId()));
    }
}
