<?php

namespace AdventureGame\Character;

use AdventureGame\Item\ContainerInterface;
use AdventureGame\Item\NameTrait;

/**
 * Class Character is a player (PC), or non-player (NPC) that exists within the game.
 * @package AdventureGame\Character
 */
class Character
{
    use NameTrait;

    public function __construct(string $name, public ContainerInterface $inventory)
    {
        $this->name = $name;
    }
}