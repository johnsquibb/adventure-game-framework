<?php

namespace AdventureGame\Item;

use PHPUnit\Framework\TestCase;

class ItemTest extends TestCase
{
    public function testCreateItem()
    {
        $id = 'test-item';
        $name = 'Test Item';
        $description = 'Test Item Description';
        $item = new Item($id, $name, $description);

        $this->assertEquals($id, $item->id);
        $this->assertEquals($name, $item->name);
        $this->assertEquals($description, $item->description);
    }
}
