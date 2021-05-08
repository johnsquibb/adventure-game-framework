<?php

namespace AdventureGame\Test\Location;

use AdventureGame\Location\Portal;
use PHPUnit\Framework\TestCase;

class PortalTest extends TestCase
{
    public function testPortalCreate()
    {
        $id = 'test-door';
        $direction = 'north';
        $destinationLocationId = 'test-room';

        $door = new Portal($id, $direction, $destinationLocationId);
        $this->assertEquals($id, $door->id);
        $this->assertEquals($direction, $door->direction);
        $this->assertEquals($destinationLocationId, $door->destinationLocationId);
    }
}
