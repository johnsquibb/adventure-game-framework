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
     * Remove an item from player inventory.
     * @param Item $item
     */
    public function removeItemFromPlayerInventory(Item $item): void
    {
        $this->player->inventory->removeItemById($item->id);
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

    /**
     * Get all items matching tag from player inventory.
     * @param string $tag
     * @return array
     */
    public function getItemsByTagFromPlayerInventory(string $tag): array
    {
        return $this->player->inventory->getItemsByTag($tag);
    }
}