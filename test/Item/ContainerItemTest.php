<?php

namespace AdventureGame\Test\Item;

use AdventureGame\Item\Container;
use AdventureGame\Item\ContainerItem;
use AdventureGame\Item\Item;
use PHPUnit\Framework\TestCase;

class ContainerItemTest extends TestCase
{
    public function testCreateItem()
    {
        $id = 'test-item';
        $name = 'Test Item';
        $description = 'Test Item Description';
        $tag = 'test';
        $item = new ContainerItem($id, $name, $description, $tag);

        $this->assertEquals($id, $item->id);
        $this->assertEquals($name, $item->name);
        $this->assertEquals($description, $item->description);
        $this->assertEquals($tag, $item->tag);
    }

    public function testAddContainerItemToContainer()
    {
        $container = new Container();
        $item = new ContainerItem(
            'test-item',
            'Test Item',
            'Test Item Description',
            'test'
        );
        $container->addItem($item);
        $this->assertEquals($item, $container->getItemById($item->id));
    }

    public function testAddContainerItemToContainerItem()
    {
        $container = new ContainerItem(
            'test-containerItem',
            'Test ContainerItem',
            'Test ContainerItem Description',
            'test-container'
        );

        $item = new ContainerItem(
            'test-item',
            'Test Item',
            'Test Item Description',
            'test'
        );
        $container->addItem($item);
        $this->assertEquals($item, $container->getItemById($item->id));
    }

    public function testGetContainerItemFromContainer()
    {
        $container = new Container();
        $this->assertNull($container->getItemById('nothing'));

        $item = new ContainerItem(
            'test-item',
            'Test Item',
            'Test Item Description',
            'test'
        );
        $container->addItem($item);
        $this->assertEquals($item, $container->getItemById($item->id));
    }

    public function testGetContainerItemFromContainerIem()
    {
        $container = new ContainerItem(
            'test-containerItem',
            'Test ContainerItem',
            'Test ContainerItem Description',
            'test-container'
        );

        $this->assertNull($container->getItemById('nothing'));

        $item = new ContainerItem(
            'test-item',
            'Test Item',
            'Test Item Description',
            'test'
        );
        $container->addItem($item);
        $this->assertEquals($item, $container->getItemById($item->id));
    }

    public function testGetContainerItemsFromContainerByTag()
    {
        $container = new Container();
        $this->assertEmpty($container->getItemsByTag('test'));

        $item = new ContainerItem(
            'test-item',
            'Test Item',
            'Test Item Description',
            'test'
        );
        $container->addItem($item);

        $this->assertEquals([$item], $container->getItemsByTag($item->tag));

        $item2 = new ContainerItem(
            'test-item',
            'Test Item',
            'Test Item Description',
            'test'
        );
        $container->addItem($item);

        $this->assertEquals([$item, $item2], $container->getItemsByTag($item->tag));
    }

    public function testGetContainerItemsFromContainerItemByTag()
    {
        $container = new ContainerItem(
            'test-containerItem',
            'Test ContainerItem',
            'Test ContainerItem Description',
            'test-container'
        );

        $this->assertEmpty($container->getItemsByTag('test'));

        $item = new ContainerItem(
            'test-item',
            'Test Item',
            'Test Item Description',
            'test'
        );
        $container->addItem($item);

        $this->assertEquals([$item], $container->getItemsByTag($item->tag));

        $item2 = new ContainerItem(
            'test-item',
            'Test Item',
            'Test Item Description',
            'test'
        );
        $container->addItem($item);

        $this->assertEquals([$item, $item2], $container->getItemsByTag($item->tag));
    }

    public function testRemoveContainerItemFromContainer()
    {
        $container = new Container();
        $item = new ContainerItem(
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

    public function testRemoveContainerItemFromContainerItem()
    {
        $container = new ContainerItem(
            'test-containerItem',
            'Test ContainerItem',
            'Test ContainerItem Description',
            'test-container'
        );

        $item = new ContainerItem(
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
