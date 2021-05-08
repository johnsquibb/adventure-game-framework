<?php

namespace AdventureGame\Item;

class Item implements ItemInterface
{
    public function __construct(
        public string $id,
        public string $name,
        public string $description,
        public string $tag
    ) {
    }
}