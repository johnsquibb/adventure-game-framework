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
            ['test']
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
            ['test']
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
            ['test']
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
            ['test']
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
            ['test']
        );
        $container->addItem($item);

        $this->assertEquals([$item], $container->getItemsByTag('test'));

        $item2 = new Item(
            'test-item-2',
            'Test Item 2',
            'Test Item 2 Description',
            ['test']
        );
        $container->addItem($item2);

        $this->assertEquals([$item, $item2], $container->getItemsByTag('test'));
    }

    public function testGetItemsByTypeAndTag()
    {
        $container = new Container();
        $this->assertEmpty($container->getItemsByTag('test'));

        $item = new Item(
            'test-item',
            'Test Item',
            'Test Item Description',
            ['test']
        );
        $container->addItem($item);

        $item2 = new ContainerItem(
            'test-item-2',
            'Test Item 2',
            'Test Item 2 Description',
            ['test']
        );
        $container->addItem($item2);

        $this->assertEquals(
            [$item, $item2],
            $container->getItemsByTypeAndTag(
                ItemInterface::class,
                'test'
            )
        );

        $this->assertEquals(
            [$item2],
            $container->getItemsByTypeAndTag(
                ContainerInterface::class,
                'test'
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
            ['test']
        );

        $container->addItem($item);
        $this->assertEquals($item, $container->getItemById($item->getId()));

        $container->removeItemById($item->getId());
        $this->assertNull($container->getItemById($item->getId()));
    }

    public function testCapacity()
    {
        $container = new Container();

        $this->assertEquals(0, $container->getCapacity());

        $container->setCapacity(999);
        $this->assertEquals(999, $container->getCapacity());

        $container->setCapacity(-999);
        $this->assertEquals(-999, $container->getCapacity());

        $container->setCapacity(0);
        $this->assertEquals(0, $container->getCapacity());
    }
}
