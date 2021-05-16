<?php

namespace AdventureGame\Item;

use AdventureGame\Entity\AccessibleEntityInterface;
use AdventureGame\Entity\LockableInterface;

/**
 * Interface ContainerItemInterface defines methods for items that are also containers.
 * @package AdventureGame\Item
 */
interface ContainerItemInterface extends ContainerInterface, AccessibleEntityInterface,
                                         LockableInterface
{

}