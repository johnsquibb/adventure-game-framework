<?php

namespace AdventureGame\Game;

use AdventureGame\Character\Character;
use AdventureGame\Item\Item;

class PlayerController
{
    public function __construct(private Character $player)
    {

    }

    /**
     * Add an item to player inventory.
     * @param Item $item
     */
    public function addItemToPlayerInventory(Item $item): void
    {
        $this->player->inventory->addItem($item);
    }

    /**
     * Get an item by id from player inventory, if it exists.
     * @param string $itemId
     * @return Item|null
     */
    public function getItemByIdFromPlayerInventory(string $itemId): ?Item
    {
        return $this->player->inventory->getItemById($itemId);
    }
}