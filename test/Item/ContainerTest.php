<?php

namespace AdventureGame\Test\Item;

use AdventureGame\Item\Container;
use AdventureGame\Item\ContainerInterface;
use AdventureGame\Item\ContainerItem;
use AdventureGame\Item\Item;
use AdventureGame\Item\ItemInterface;
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
        $this->assertEquals($item, $container->getItemById($item->getId()));
    }

    public function testCountItems()
    {
        $container = new Container();
        $item = new Item(
            'test-item',
            'Test Item',
            'Test Item Description',
            'test'
        );
        $container->addItem($item);
        $this->assertEquals(1, $container->countItems());
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
        $this->assertEquals($item, $container->getItemById($item->getId()));
    }

    public function testGetItems()
    {
        $container = new Container();
        $item = new Item(
            'test-item',
            'Test Item',
            'Test Item Description',
            'test'
        );

        $container->addItem($item);
        $this->assertEquals([$item], $container->getItems());
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

        $this->assertEquals([$item], $container->getItemsByTag($item->getTag()));

        $item2 = new Item(
            'test-item-2',
            'Test Item 2',
            'Test Item 2 Description',
            'test'
        );
        $container->addItem($item2);

        $this->assertEquals([$item, $item2], $container->getItemsByTag($item->getTag()));
    }

    public function testGetItemsByTypeAndTag()
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

        $item2 = new ContainerItem(
            'test-item-2',
            'Test Item 2',
            'Test Item 2 Description',
            'test'
        );
        $container->addItem($item2);

        $this->assertEquals(
            [$item, $item2],
            $container->getItemsByTypeAndTag(
                ItemInterface::class,
                $item->getTag()
            )
        );

        $this->assertEquals(
            [$item2],
            $container->getItemsByTypeAndTag(
                ContainerInterface::class,
                $item->getTag()
            )
        );
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
        $this->assertEquals($item, $container->getItemById($item->getId()));

        $container->removeItemById($item->getId());
        $this->assertNull($container->getItemById($item->getId()));
    }
}
