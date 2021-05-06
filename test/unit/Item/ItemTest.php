<?php

namespace AdventureGame\Item;

use PHPUnit\Framework\TestCase;

class ItemTest extends TestCase
{
    public function testCreateItem()
    {
        $id = 'test-item';
        $name = 'Test Item';
        $item = new Item($id, $name);

        $this->assertEquals($id, $item->id);
        $this->assertEquals($name, $item->name);
    }
}
