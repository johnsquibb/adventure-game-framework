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

        $room = new Location($id, $name, new Container(), []);
        $this->assertEquals($id, $room->id);
        $this->assertEquals($name, $room->name);
    }

    public function testLocationExits()
    {
        $id = 'test-room';
        $name = 'The Test Room';

        $portal = new Portal('test-door', 'north', 'test-room');
        $exits = [$portal];

        $room = new Location($id, $name, new Container(), $exits);
        $this->assertEquals($id, $room->id);
        $this->assertEquals($name, $room->name);

        $this->assertEquals($portal, $room->getExitInDirection($portal->direction));
        $this->assertNull($room->getExitInDirection('nowhere'));
    }

    public function testLocationItems()
    {
        $id = 'test-room';
        $name = 'The Test Room';
        $items = new Container();
        $item = new Item('test-item', 'Test Item');
        $items->addItem($item);

        $room = new Location($id, $name, $items, []);
        $this->assertEquals($item, $room->items->getItem($item->id));
    }
}
