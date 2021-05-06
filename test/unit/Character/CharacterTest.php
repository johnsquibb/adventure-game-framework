<?php

namespace AdventureGame\Character;

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
        $this->assertEquals($name, $character->name);
    }

    public function testAddItemToPlayerInventory()
    {
        $name = 'test-character';
        $inventory = new Container();
        $character = new Character($name, $inventory);

        $item = new Item('test-item', 'Test Item', 'Test Item Description');
        $character->inventory->addItem($item);

        $this->assertEquals($item, $character->inventory->getItemById($item->id));
    }

    public function testGetItemFromPlayerInventory()
    {
        $name = 'test-character';
        $inventory = new Container();
        $character = new Character($name, $inventory);

        $this->assertNull($character->inventory->getItemById('nothing'));

        $item = new Item('test-item', 'Test Item', 'Test Item Description');
        $character->inventory->addItem($item);

        $this->assertEquals($item, $character->inventory->getItemById($item->id));
    }
}
