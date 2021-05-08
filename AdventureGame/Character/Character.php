<?php

namespace AdventureGame\Character;

use AdventureGame\Item\ContainerInterface;

/**
 * Class Character is a player (PC), or non-player (NPC) that exists within the game.
 * @package AdventureGame\Character
 */
class Character
{
    public function __construct(public string $name, public ContainerInterface $inventory)
    {
    }
}