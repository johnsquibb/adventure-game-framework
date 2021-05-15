<?php

namespace AdventureGame\Test\Character;

use AdventureGame\Character\Character;
use AdventureGame\Item\Container;
use AdventureGame\Item\Item;
use PHPUnit\Framework\TestCase;

class CharacterTest extends TestCase
{
    public function testCreatePlayer()
    {
        $name = 'test-character';
        $inventory = new Container();
        $character = new Character($name, $inventory);
        $this->assertEquals($name, $character->getName());
    }

    public function testAddItemToPlayerInventory()
    {
        $name = 'test-character';
        $inventory = new Container();
        $character = new Character($name, $inventory);

        $item = new Item(
            'test-item',
            'Test Item',
            'Test Item Description',
            'test'
        );
        $character->inventory->addItem($item);

        $this->assertEquals($item, $character->inventory->getItemById($item->getId()));
    }

    public function testGetItemFromPlayerInventory()
    {
        $name = 'test-character';
        $inventory = new Container();
        $character = new Character($name, $inventory);

        $this->assertNull($character->inventory->getItemById('nothing'));

        $item = new Item(
            'test-item',
            'Test Item',
            'Test Item Description',
            'test'
        );
        $character->inventory->addItem($item);

        $this->assertEquals($item, $character->inventory->getItemById($item->getId()));
    }
}
