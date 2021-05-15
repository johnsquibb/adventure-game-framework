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

    public function testPortalLockable()
    {
        $id = 'test-door';
        $name = 'Wooden Door';
        $description = 'A door leading to the north.';
        $direction = 'north';
        $destinationLocationId = 'test-room';

        $door = new Portal($id, $name, $description, $direction, $destinationLocationId);

        $this->assertFalse($door->getLocked());

        $door->setLocked(true);
        $this->assertTrue($door->getLocked());

        $door->setLocked(false);
        $this->assertFalse($door->getLocked());

        $door->setKeyEntityId('theKey');
        $this->assertEquals('theKey', $door->getKeyEntityId());
    }
}
