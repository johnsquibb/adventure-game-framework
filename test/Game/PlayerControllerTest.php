<?php

namespace AdventureGame\Test\Game;

use AdventureGame\Character\Character;
use AdventureGame\Game\PlayerController;
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
}
