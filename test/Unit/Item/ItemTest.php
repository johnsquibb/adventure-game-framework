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
        $tag = 'test';
        $item = new Item($id, $name, $description, $tag);

        $this->assertEquals($id, $item->id);
        $this->assertEquals($name, $item->name);
        $this->assertEquals($description, $item->description);
        $this->assertEquals($tag, $item->tag);
    }
}
