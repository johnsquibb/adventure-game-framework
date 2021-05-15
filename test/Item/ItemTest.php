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
        $tag = 'test';
        $item = new Item($id, $name, $description, $tag);

        $this->assertEquals($id, $item->getId());
        $this->assertEquals($name, $item->getName());
        $this->assertEquals($description, $item->getDescription());
        $this->assertEquals($tag, $item->getTag());
    }
}
