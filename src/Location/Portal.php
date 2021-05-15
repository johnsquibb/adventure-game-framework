<?php

namespace AdventureGame\Location;

use AdventureGame\Item\DescriptionTrait;
use AdventureGame\Item\IdentityTrait;
use AdventureGame\Item\NameTrait;

/**
 * Class Portal provides a directional exit from one Location to another destination.
 * @package AdventureGame\Location
 */
class Portal
{
    use IdentityTrait;
    use NameTrait;
    use DescriptionTrait;

    public function __construct(
        string $id,
        string $name,
        string $description,
        public string $direction,
        public string $destinationLocationId,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
    }
}