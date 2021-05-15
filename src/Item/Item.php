<?php

namespace AdventureGame\Item;

/**
 * Class Item is an object that can be taken, dropped, or stored in a container.
 * @package AdventureGame\Item
 */
class Item implements ItemInterface
{
    use IdentityTrait;
    use NameTrait;
    use DescriptionTrait;
    use TagTrait;

    public function __construct(
        string $id,
        string $name,
        string $description,
        string $tag
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->tag = $tag;
    }
}