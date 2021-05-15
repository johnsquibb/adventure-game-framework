<?php

namespace AdventureGame\Item;

use AdventureGame\Entity\InteractionEntityInterface;

/**
 * Interface ContainerItemInterface defines methods for items that are also containers.
 * @package AdventureGame\Item
 */
interface ContainerItemInterface extends ContainerInterface, InteractionEntityInterface
{

}