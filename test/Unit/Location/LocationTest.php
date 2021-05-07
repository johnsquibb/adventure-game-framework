<?php

namespace AdventureGame\Location;

use AdventureGame\Item\Container;
use AdventureGame\Item\Item;
use PHPUnit\Framework\TestCase;

class LocationTest extends TestCase
{
    public function testLocationCreate()
    {
        $id = 'test-room';
        $name = 'The Test Room';
        $description = 'This is a test room';
        $items = new Container();

        $room = new Location($id, $name, $description, $items, []);
        $this->assertEquals($id, $room->id);
        $this->assertEquals($name, $room->name);
        $this->assertEquals($description, $room->description);
        $this->assertEquals($items, $room->items);
    }

    public function testLocationExits()
    {
        $id = 'test-room';
        $name = 'The Test Room';
        $description = 'This is a test room';
        $items = new Container();

        $portal = new Portal('test-door', 'north', 'test-room');
        $exits = [$portal];

        $room = new Location($id, $name, $description, $items, $exits);
        $this->assertEquals($id, $room->id);
        $this->assertEquals($name, $room->name);

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
            'Test Item Description',
            'test'
        );
        $items->addItem($item);

        $room = new Location($id, $name, $description, $items, []);
        $this->assertEquals($item, $room->items->getItemById($item->id));
    }
}
