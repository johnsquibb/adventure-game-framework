<?php

namespace AdventureGame\Item;

trait ContainerTrait
{
    private array $items = [];

    /**
     * Add an item.
     * @param ItemInterface $item
     */
    public function addItem(ItemInterface $item): void
    {
        $this->items[] = $item;
    }

    /**
     * Get an item by id, if it exists.
     * @param string $itemId
     * @return ItemInterface|null
     */
    public function getItemById(string $itemId): ?ItemInterface
    {
        foreach ($this->items as $item) {
            if ($item->id === $itemId) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Get all items that match a tag.
     * @param string $tag
     * @return array
     */
    public function getItemsByTag(string $tag): array
    {
        $items = [];
        foreach ($this->items as $item) {
            if ($item->tag === $tag) {
                $items[] = $item;
            }
        }

        return $items;
    }

    /**
     * Remove an item by id, if it exists.
     * @param string $itemId
     */
    public function removeItemById(string $itemId): void
    {
        foreach ($this->items as $key => $item) {
            if ($item->id === $itemId) {
                unset($this->items[$key]);
            }
        }
    }
}