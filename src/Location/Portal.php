<?php

namespace AdventureGame\Location;

use AdventureGame\Entity\LockableEntityInterface;
use AdventureGame\Entity\TaggableEntityInterface;
use AdventureGame\Traits\DescriptionTrait;
use AdventureGame\Traits\IdentityTrait;
use AdventureGame\Traits\LockableTrait;
use AdventureGame\Traits\NameTrait;
use AdventureGame\Traits\TagTrait;

/**
 * Class Portal provides a directional exit from one Location to another destination.
 * @package AdventureGame\Location
 */
class Portal implements TaggableEntityInterface, LockableEntityInterface
{
    use IdentityTrait;
    use NameTrait;
    use DescriptionTrait;
    use LockableTrait;
    use TagTrait;

    public function __construct(
        string $id,
        string $name,
        string $description,
        array $tags,
        public string $direction,
        public string $destinationLocationId,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->tags = $tags;
    }
}