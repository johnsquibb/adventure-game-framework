<?php

namespace AdventureGame\Item;

use AdventureGame\Entity\AccessibleEntityInterface;
use AdventureGame\Entity\AcquirableEntityInterface;
use AdventureGame\Entity\ActivatableEntityInterface;

/**
 * Interface ItemInterface defines methods for objects that behave like items.
 * @package AdventureGame\Item
 */
interface ItemInterface extends AccessibleEntityInterface, AcquirableEntityInterface,
                                ActivatableEntityInterface
{
}