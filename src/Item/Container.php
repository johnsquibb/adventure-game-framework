<?php

namespace AdventureGame\Item;

use AdventureGame\Traits\ContainerTrait;

/**
 * Class Container is an object that contains items. Use for containers that don't need to be
 * identified by tag or interacted with directly.
 * @package AdventureGame\Item
 */
class Container implements ContainerInterface
{
    use ContainerTrait;
}