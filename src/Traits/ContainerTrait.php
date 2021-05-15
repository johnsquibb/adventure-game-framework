<?php

namespace AdventureGame\Traits;

use AdventureGame\Item\ItemInterface;

/**
 * Trait ContainerTrait provides methods for objects implementing ContainerInterface.
 * @package AdventureGame\Item
 */
trait ContainerTrait
{
    use LockableTrait;

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
     * Return count of items.
     * @return int the number of $items
     */
    public function countItems(): int
    {
        return count($this->items);
    }

    /**
     * Get an item by id, if it exists.
     * @param string $itemId
     * @return ItemInterface|null
     */
    public function getItemById(string $itemId): ?ItemInterface
    {
        foreach ($this->items as $item) {
            if ($item->getId() === $itemId) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Return all items.
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
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
            if ($item->getTag() === $tag) {
                $items[] = $item;
            }
        }

        return $items;
    }

    /**
     * Get all items of a particular type that match a tag.
     * @param string $type Class type to match
     * @param string $tag Tag to match
     * @return array
     */
    public function getItemsByTypeAndTag(string $type, string $tag): array
    {
        $items = [];
        foreach ($this->items as $item) {
            if (is_a($item, $type) && $item->getTag() === $tag) {
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
            if ($item->getId() === $itemId) {
                unset($this->items[$key]);
            }
        }
    }
}