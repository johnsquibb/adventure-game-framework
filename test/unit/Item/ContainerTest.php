<?php

namespace AdventureGame\Item;

use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    public function testAddItem()
    {
        $container = new Container();
        $item = new Item('test-item', 'Test Item');
        $container->addItem($item);
        $this->assertEquals($item, $container->getItem($item->id));
    }

    public function testGetItem()
    {
        $container = new Container();
        $this->assertNull($container->getItem('nothing'));

        $item = new Item('test-item', 'Test Item');
        $container->addItem($item);
        $this->assertEquals($item, $container->getItem($item->id));
    }
}
