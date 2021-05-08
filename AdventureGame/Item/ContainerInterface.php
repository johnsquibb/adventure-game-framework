<?php

namespace AdventureGame\Item;

interface ContainerInterface
{
    public function addItem(Item $item): void;

    public function getItemById(string $itemId): ?ItemInterface;

    public function getItemsByTag(string $tag): array;

    public function removeItemById(string $itemId): void;
}