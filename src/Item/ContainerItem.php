<?php

namespace AdventureGame\Item;

/**
 * Class ContainerItem is an item that can contain other items. Use for containers that can be
 * identified and interacted with.
 * @package AdventureGame\Item
 */
class ContainerItem extends Item implements ContainerInterface, ItemInterface
{
    use ContainerTrait;
}