<?php

namespace AdventureGame\Location;

/**
 * Class Map is a collection of Locations that are interrelated.
 * @package AdventureGame\Location
 */
class Map
{
    public function __construct(public string $id, private array $locations)
    {
    }

    /**
     * Get location by id if it exists.
     * @param string $id
     * @return Location|null
     */
    public function getLocationById(string $id): ?Location
    {
        foreach ($this->locations as $location) {
            if ($location->id === $id) {
                return $location;
            }
        }

        return null;
    }
}