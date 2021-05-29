<?php

namespace AdventureGame\Test\Item;

use AdventureGame\Item\Item;
use PHPUnit\Framework\TestCase;

class ItemTest extends TestCase
{
    public function testCreateItem()
    {
        $id = 'test-item';
        $name = 'Test Item';
        $description = 'Test Item Description';
        $tags = ['test'];
        $item = new Item($id, $name, $description, $tags);

        $this->assertEquals($id, $item->getId());
        $this->assertEquals($name, $item->getName());
        $this->assertEquals($description, $item->getDescription());
        $this->assertEquals($tags, $item->getTags());
    }

    public function testItemDiscovered()
    {
        $item = new Item('', '', '', ['']);

        $this->assertFalse($item->getDiscovered());

        $item->setDiscovered(false);
        $this->assertFalse($item->getDiscovered());
    }

    public function testItemAccessible()
    {
        $item = new Item('', '', '', ['']);

        $this->assertTrue($item->getAccessible());

        $item->setAccessible(false);
        $this->assertFalse($item->getAccessible());
    }

    public function testItemAcquirable()
    {
        $item = new Item('', '', '', ['']);

        $this->assertTrue($item->getAcquirable());
        $item->setAcquirable(false);
        $this->assertFalse($item->getAcquirable());

        $item->setAcquirable(true);
        $this->assertTrue($item->getAcquirable());
    }
}
