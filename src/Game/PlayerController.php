<?php

namespace AdventureGame\Game;

use AdventureGame\Character\Character;
use AdventureGame\Item\ItemInterface;

/**
 * Class PlayerController provides methods for player and inventory manipulation.
 * @package AdventureGame\Game
 */
class PlayerController
{
    public function __construct(private Character $player)
    {
    }

    /**
     * Add an item to player inventory.
     * @param ItemInterface $item
     */
    public function addItemToPlayerInventory(ItemInterface $item): void
    {
        $this->player->inventory->addItem($item);
    }

    /**
     * Get an item by id from player inventory, if it exists.
     * @param string $itemId
     * @return ItemInterface|null
     */
    public function getItemByIdFromPlayerInventory(string $itemId): ?ItemInterface
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

    /**
     * Remove an item from player inventory.
     * @param ItemInterface $item
     */
    public function removeItemFromPlayerInventory(ItemInterface $item): void
    {
        $this->player->inventory->removeItemById($item->getId());
    }
}