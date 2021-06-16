<?php

namespace AdventureGame\Test\Location;

use AdventureGame\Item\Container;
use AdventureGame\Location\Location;
use AdventureGame\Location\Map;
use PHPUnit\Framework\TestCase;

class MapTest extends TestCase
{
    public function testCreateMap()
    {
        $id = 'test-map';
        $map = new Map($id, []);
        $this->assertEquals($id, $map->getId());
    }

    public function testMapLocations()
    {
        $location = new Location(
            'test-location',
            'Test Location',
            ['Test Description'],
            new Container(), []
        );
        $locations = [$location];

        $map = new Map('test-map', $locations);
        $this->assertEquals($location, $map->getLocationById($location->getId()));
    }
}
