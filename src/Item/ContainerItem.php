<?php

namespace AdventureGame\Item;

use AdventureGame\Traits\ContainerTrait;
use AdventureGame\Traits\LockableTrait;

/**
 * Class ContainerItem is an item that can contain other items. Use for containers that can be
 * identified and interacted with.
 * @package AdventureGame\Item
 */
class ContainerItem extends Item implements ContainerItemInterface
{
    use ContainerTrait;
    use LockableTrait;
}