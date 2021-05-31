<?php

namespace AdventureGame\Test\Game;

use AdventureGame\Character\Character;
use AdventureGame\Game\PlayerController;
use AdventureGame\Item\AbstractItem;
use AdventureGame\Item\Container;
use AdventureGame\Item\Item;
use PHPUnit\Framework\TestCase;

class PlayerControllerTest extends TestCase
{
    public function testAddItemToPlayerInventory()
    {
        $inventory = new Container();
        $player = new Character('test-player', $inventory);
        $playerController = new PlayerController($player);

        $item = new Item(
            'test-item',
            'Test Item',
            'Test Item Description',
            ['test']
        );
        $playerController->addItemToPlayerInventory($item);

        $this->assertEquals(
            $item,
            $playerController->getItemByIdFromPlayerInventory($item->getId())
        );
        $this->assertEquals($item, $inventory->getItemById($item->getId()));
    }

    public function testCreatePlayerController()
    {
        $player = new Character('test-player', new Container());
        $playerController = new PlayerController($player);
        $this->assertNull($playerController->getItemByIdFromPlayerInventory('nothing'));
    }

    public function testGetItemFromPlayerInventory()
    {
        $inventory = new Container();
        $player = new Character('test-player', $inventory);
        $playerController = new PlayerController($player);

        $item = new Item(
            'test-item',
            'Test Item',
            'Test Item Description',
            ['test']
        );
        $this->assertNull($playerController->getItemByIdFromPlayerInventory($item->getId()));

        $playerController->addItemToPlayerInventory($item);

        $this->assertEquals(
            $item,
            $playerController->getItemByIdFromPlayerInventory($item->getId())
        );
        $this->assertEquals($item, $inventory->getItemById($item->getId()));
    }

    public function testGetItemsByTagFromPlayerInventory()
    {
        $inventory = new Container();
        $player = new Character('test-player', $inventory);
        $playerController = new PlayerController($player);

        $item = new Item(
            'test-item',
            'Test Item',
            'Test Item Description',
            ['test']
        );

        $this->assertEmpty($playerController->getItemsByTagFromPlayerInventory('test'));

        $playerController->addItemToPlayerInventory($item);
        $this->assertEquals(
            [$item],
            $playerController->getItemsByTagFromPlayerInventory('test')
        );
    }

    public function testGetInventoryCapacityCanAccommodate()
    {
        $inventory = new Container();
        $player = new Character('test-player', $inventory);
        $playerController = new PlayerController($player);

        $item = new Item(
            'test-item',
            'Test Item',
            'Test Item Description',
            ['test']
        );
        $item->setSize(1);

        $inventory->setCapacity(0);
        $this->assertFalse($playerController->getPlayerInventory()->hasCapacity($item->getSize()));

        $inventory->setCapacity($item->getSize());
        $this->assertTrue($playerController->getPlayerInventory()->hasCapacity($item->getSize()));

        $inventory->setCapacity($item->getSize() * 2);
        $this->assertTrue($playerController->getPlayerInventory()->hasCapacity($item->getSize() * 2));

        $item->setSize(-1);
        $inventory->setCapacity(0);
        $this->assertTrue($playerController->getPlayerInventory()->hasCapacity($item->getSize()));
    }

    public function testGetInventoryCanAccommodateInventoryContainsItems()
    {
        $inventory = new Container();
        $player = new Character('test-player', $inventory);
        $playerController = new PlayerController($player);

        $item = new Item(
            'test-item',
            'Test Item',
            'Test Item Description',
            ['test']
        );

        $item->setSize(1);
        $inventory->setCapacity(2);

        $this->assertTrue($playerController->getPlayerInventory()->hasCapacity($item->getSize()));
        $inventory->addItem($item);

        $this->assertTrue($playerController->getPlayerInventory()->hasCapacity($item->getSize()));

        $inventory->addItem($item);
        $this->assertFalse($playerController->getPlayerInventory()->hasCapacity($item->getSize()));

        $inventory->removeItemById($item->getId());
        $this->assertTrue($playerController->getPlayerInventory()->hasCapacity($item->getSize()));
    }
}
