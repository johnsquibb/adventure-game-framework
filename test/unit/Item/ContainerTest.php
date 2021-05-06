<?php

namespace AdventureGame\Item;

use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    public function testAddItem()
    {
        $container = new Container();
        $item = new Item('test-item', 'Test Item', 'Test Item Description');
        $container->addItem($item);
        $this->assertEquals($item, $container->getItemById($item->id));
    }

    public function testGetItem()
    {
        $container = new Container();
        $this->assertNull($container->getItemById('nothing'));

        $item = new Item('test-item', 'Test Item', 'Test Item Description');
        $container->addItem($item);
        $this->assertEquals($item, $container->getItemById($item->id));
    }

    public function testRemoveItem()
    {
        $container = new Container();
        $item = new Item('test-item', 'Test Item', 'Test Item Description');

        $container->addItem($item);
        $this->assertEquals($item, $container->getItemById($item->id));

        $container->removeItemById($item->id);
        $this->assertNull($container->getItemById($item->id));
    }
}
