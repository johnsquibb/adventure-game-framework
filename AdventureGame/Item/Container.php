<?php

namespace AdventureGame\Item;

class Container
{
    private array $items = [];

    /**
     * Add an item.
     * @param Item $item
     */
    public function addItem(Item $item): void
    {
        $this->items[] = $item;
    }

    /**
     * Get an item by id, if it exists.
     * @param string $itemId
     * @return Item|null
     */
    public function getItem(string $itemId): ?Item
    {
        foreach ($this->items as $item) {
            if ($item->id === $itemId) {
                return $item;
            }
        }

        return null;
    }
}