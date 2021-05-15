<?php

namespace AdventureGame\Test\Item;

use AdventureGame\Item\Container;
use AdventureGame\Item\ContainerItem;
use AdventureGame\Item\Item;
use PHPUnit\Framework\TestCase;

class ContainerItemTest extends TestCase
{
    public function testCreateContainerItem()
    {
        $id = 'test-item';
        $name = 'Test Item';
        $description = 'Test Item Description';
        $tag = 'test';
        $item = new ContainerItem($id, $name, $description, $tag);

        $this->assertEquals($id, $item->getId());
        $this->assertEquals($name, $item->getName());
        $this->assertEquals($description, $item->getDescription());
        $this->assertEquals($tag, $item->getTag());
    }

    public function testContainerItemAccessible()
    {
        $item = new ContainerItem('','','','');

        $this->assertFalse($item->getAccessible());
        $item->setAccessible(true);
        $this->assertTrue($item->getAccessible());

        $item->setAccessible(false);
        $this->assertFalse($item->getAccessible());
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
        $this->assertEquals($item, $container->getItemById($item->getId()));
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
        $this->assertEquals($item, $container->getItemById($item->getId()));
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
        $this->assertEquals($item, $container->getItemById($item->getId()));
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
        $this->assertEquals($item, $container->getItemById($item->getId()));
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

        $this->assertEquals([$item], $container->getItemsByTag($item->getTag()));

        $item2 = new ContainerItem(
            'test-item',
            'Test Item',
            'Test Item Description',
            'test'
        );
        $container->addItem($item);

        $this->assertEquals([$item, $item2], $container->getItemsByTag($item->getTag()));
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

        $this->assertEquals([$item], $container->getItemsByTag($item->getTag()));

        $item2 = new ContainerItem(
            'test-item',
            'Test Item',
            'Test Item Description',
            'test'
        );
        $container->addItem($item);

        $this->assertEquals([$item, $item2], $container->getItemsByTag($item->getTag()));
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
        $this->assertEquals($item, $container->getItemById($item->getId()));

        $container->removeItemById($item->getId());
        $this->assertNull($container->getItemById($item->getId()));
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
        $this->assertEquals($item, $container->getItemById($item->getId()));

        $container->removeItemById($item->getId());
        $this->assertNull($container->getItemById($item->getId()));
    }
}
