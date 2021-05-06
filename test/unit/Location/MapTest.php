<?php

namespace AdventureGame\Location;

use AdventureGame\Item\Container;
use PHPUnit\Framework\TestCase;

class MapTest extends TestCase
{
    public function testCreateMap()
    {
        $id = 'test-map';
        $map = new Map($id, []);
        $this->assertEquals($id, $map->id);
    }

    public function testMapLocations()
    {
        $location = new Location('test-location', 'Test Location', new Container(), []);
        $locations = [$location];

        $map = new Map('test-map', $locations);
        $this->assertEquals($location, $map->getLocationById($location->id));
    }
}
