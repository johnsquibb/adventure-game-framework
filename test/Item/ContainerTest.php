<?php

namespace AdventureGame\Test\Item;

use AdventureGame\Item\Container;
use AdventureGame\Item\Item;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    public function testAddItem()
    {
        $container = new Container();
        $item = new Item(
            'test-item',
            'Test Item',
            'Test Item Description',
            'test'
        );
        $container->addItem($item);
        $this->assertEquals($item, $container->getItemById($item->id));
    }

    public function testGetItem()
    {
        $container = new Container();
        $this->assertNull($container->getItemById('nothing'));

        $item = new Item(
            'test-item',
            'Test Item',
            'Test Item Description',
            'test'
        );
        $container->addItem($item);
        $this->assertEquals($item, $container->getItemById($item->id));
    }

    public function testGetItemsByTag()
    {
        $container = new Container();
        $this->assertEmpty($container->getItemsByTag('test'));

        $item = new Item(
            'test-item',
            'Test Item',
            'Test Item Description',
            'test'
        );
        $container->addItem($item);

        $this->assertEquals([$item], $container->getItemsByTag($item->tag));

        $item2 = new Item(
            'test-item',
            'Test Item',
            'Test Item Description',
            'test'
        );
        $container->addItem($item);

        $this->assertEquals([$item, $item2], $container->getItemsByTag($item->tag));
    }

    public function testRemoveItem()
    {
        $container = new Container();
        $item = new Item(
            'test-item',
            'Test Item',
            'Test Item Description',
            'test'
        );

        $container->addItem($item);
        $this->assertEquals($item, $container->getItemById($item->id));

        $container->removeItemById($item->id);
        $this->assertNull($container->getItemById($item->id));
    }
}
