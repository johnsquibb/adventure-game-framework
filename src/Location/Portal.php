<?php

namespace AdventureGame\Location;

use AdventureGame\Entity\EntityInterface;
use AdventureGame\Entity\LockableInterface;
use AdventureGame\Traits\DescriptionTrait;
use AdventureGame\Traits\IdentityTrait;
use AdventureGame\Traits\LockableTrait;
use AdventureGame\Traits\NameTrait;

/**
 * Class Portal provides a directional exit from one Location to another destination.
 * @package AdventureGame\Location
 */
class Portal implements EntityInterface, LockableInterface
{
    use IdentityTrait;
    use NameTrait;
    use DescriptionTrait;
    use LockableTrait;

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