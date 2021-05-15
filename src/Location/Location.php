<?php

namespace AdventureGame\Location;

use AdventureGame\Item\ContainerInterface;
use AdventureGame\Traits\DescriptionTrait;
use AdventureGame\Traits\IdentityTrait;
use AdventureGame\Traits\NameTrait;

/**
 * Class Location is a place in which players and objects can exist.
 * @package AdventureGame\Location
 */
class Location
{
    use IdentityTrait;
    use NameTrait;
    use DescriptionTrait;

    public function __construct(
        string $id,
        string $name,
        string $description,
        public ContainerInterface $items,
        public array $exits,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
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