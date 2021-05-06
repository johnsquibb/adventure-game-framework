<?php

namespace AdventureGame\Location;

use AdventureGame\Item\Container;

class Location
{
    public function __construct(
        public string $id,
        public string $name,
        public string $description,
        public Container $items,
        private array $exits,
    ) {
    }

    /**
     * Get exit in specified direction, if it exists.
     * @param string $direction
     * @return Portal|null
     */
    public function getExitInDirection(string $direction): ?Portal
    {
        foreach ($this->exits as $exit) {
            if ($exit->direction === $direction) {
                return $exit;
            }
        }

        return null;
    }
}