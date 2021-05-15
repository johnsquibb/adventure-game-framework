<?php

namespace AdventureGame\Test\Location;

use AdventureGame\Location\Portal;
use PHPUnit\Framework\TestCase;

class PortalTest extends TestCase
{
    public function testPortalCreate()
    {
        $id = 'test-door';
        $name = 'Wooden Door';
        $description = 'A door leading to the north.';
        $direction = 'north';
        $destinationLocationId = 'test-room';

        $door = new Portal($id, $name, $description, $direction, $destinationLocationId);
        $this->assertEquals($id, $door->getId());
        $this->assertEquals($direction, $door->direction);
        $this->assertEquals($destinationLocationId, $door->destinationLocationId);
        $this->assertEquals($name, $door->getName());
        $this->assertEquals($description, $door->getDescription());
    }
}
