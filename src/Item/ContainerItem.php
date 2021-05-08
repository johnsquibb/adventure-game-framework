<?php

namespace AdventureGame\Item;

/**
 * Class ContainerItem is an item that can contain other items. Suitable for objects that can be
 * taken, dropped, or stored inside another container.
 * @package AdventureGame\Item
 */
class ContainerItem extends Item implements ContainerInterface, ItemInterface
{
    use ContainerTrait;
}