<?php

namespace AdventureGame\Item;

use AdventureGame\Entity\CapacityEntityInterface;
use AdventureGame\Entity\RevealableEntityInterface;

/**
 * Interface ContainerInterface defines methods for objects that behave like containers.
 * @package AdventureGame\Item
 */
interface ContainerEntityInterface extends CapacityEntityInterface, RevealableEntityInterface
{
    public function addItem(ItemInterface $item): void;

    public function countItems(): int;

    public function getItemById(string $itemId): ?ItemInterface;

    public function getItems(): array;

    public function getItemsByTag(string $tag): array;

    public function getItemsByTypeAndTag(string $type, string $tag): array;

    public function removeItemById(string $itemId): void;

    public function revealItems(): array;
}
