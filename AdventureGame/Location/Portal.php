<?php

namespace AdventureGame\Location;

class Portal
{
    public function __construct(
        public string $id,
        public string $direction,
        public string $destinationLocationId
    ) {
    }
}