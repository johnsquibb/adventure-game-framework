<?php

namespace AdventureGame\Item;

use AdventureGame\Traits\AccessibleTrait;
use AdventureGame\Traits\AcquirableTrait;
use AdventureGame\Traits\ActivatableTrait;
use AdventureGame\Traits\DeactivatableTrait;
use AdventureGame\Traits\DescriptionTrait;
use AdventureGame\Traits\DiscoverableTrait;
use AdventureGame\Traits\IdentityTrait;
use AdventureGame\Traits\NameTrait;
use AdventureGame\Traits\ReadableTrait;
use AdventureGame\Traits\TagTrait;

class AbstractItem implements ItemInterface
{
    use IdentityTrait;
    use NameTrait;
    use DescriptionTrait;
    use TagTrait;
    use AccessibleTrait;
    use DiscoverableTrait;
    use AcquirableTrait;
    use ActivatableTrait;
    use DeactivatableTrait;
    use ReadableTrait;

    public function __construct(
        string $id,
        string $name,
        string $description,
        array $tags
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->tags = $tags;
    }
}