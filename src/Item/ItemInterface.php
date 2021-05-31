<?php

namespace AdventureGame\Item;

use AdventureGame\Entity\AccessibleEntityInterface;
use AdventureGame\Entity\AcquirableEntityInterface;
use AdventureGame\Entity\ActivatableEntityInterface;
use AdventureGame\Entity\DeactivatableEntityInterface;
use AdventureGame\Entity\DescriptionInterface;
use AdventureGame\Entity\DiscoverableEntityInterface;
use AdventureGame\Entity\NameInterface;
use AdventureGame\Entity\ReadableEntityInterface;
use AdventureGame\Entity\SizeInterface;

/**
 * Interface ItemInterface defines methods for objects that behave like items.
 * @package AdventureGame\Item
 */
interface ItemInterface extends
    AccessibleEntityInterface,
    AcquirableEntityInterface,
    ActivatableEntityInterface,
    DeactivatableEntityInterface,
    ReadableEntityInterface,
    DiscoverableEntityInterface,
    SizeInterface,
    NameInterface,
    DescriptionInterface
{
}