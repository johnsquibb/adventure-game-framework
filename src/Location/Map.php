<?php

namespace AdventureGame\Location;

use AdventureGame\Item\IdentityTrait;

/**
 * Class Map is a collection of Locations that are interrelated.
 * @package AdventureGame\Location
 */
class Map
{
    use IdentityTrait;

    public function __construct(string $id, private array $locations)
    {
        $this->id = $id;
    }

    /**
     * Get location by id if it exists.
     * @param string $id
     * @return Location|null
     */
    public function getLocationById(string $id): ?Location
    {
        foreach ($this->locations as $location) {
            if ($location->getId() === $id) {
                return $location;
            }
        }

        return null;
    }
}