<?php

namespace AdventureGame\Item;

/**
 * Class Item is an object that can be taken, dropped, or stored in a container.
 * @package AdventureGame\Item
 */
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