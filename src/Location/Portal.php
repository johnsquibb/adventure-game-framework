<?php

namespace AdventureGame\Location;

/**
 * Class Portal provides a directional exit from one Location to another destination.
 * @package AdventureGame\Location
 */
class Portal
{
    public function __construct(
        public string $id,
        public string $name,
        public string $description,
        public string $direction,
        public string $destinationLocationId,
    ) {
    }
}