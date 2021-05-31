<?php

namespace AdventureGame\Game;

use AdventureGame\Character\Character;
use AdventureGame\Entity\SizeInterface;
use AdventureGame\Item\ContainerInterface;
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
     * Get player inventory.
     * @return ContainerInterface
     */
    public function getPlayerInventory(): ContainerInterface
    {
        return $this->player->inventory;
    }

    /**
     * Remove an item from player inventory.
     * @param ItemInterface $item
     */
    public function removeItemFromPlayerInventory(ItemInterface $item): void
    {
        $this->player->inventory->removeItemById($item->getId());
    }

    /**
     * Determine whether the player inventory can accommodate additional size.
     * @param int $size
     * @return bool
     */
    public function getInventoryCapacityCanAccommodate(int $size): bool
    {
        $currentSize = 0;
        foreach ($this->player->inventory->getItems() as $item) {
            if ($item instanceof SizeInterface) {
                $currentSize += $item->getSize();
            }
        }

        return $this->player->inventory->getCapacity() >= $currentSize + $size;
    }
}