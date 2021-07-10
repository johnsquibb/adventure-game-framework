<?php

namespace AdventureGame\Item;

use AdventureGame\Entity\AccessibleEntityInterface;
use AdventureGame\Entity\AcquirableEntityInterface;
use AdventureGame\Entity\LockableEntityInterface;

/**
 * Interface ContainerItemInterface defines methods for items that are also containers.
 * @package AdventureGame\Item
 */
interface ContainerItemInterface extends
    ContainerEntityInterface, AccessibleEntityInterface, AcquirableEntityInterface, LockableEntityInterface
{
}
