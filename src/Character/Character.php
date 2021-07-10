<?php

namespace AdventureGame\Character;

use AdventureGame\Item\ContainerEntityInterface;
use AdventureGame\Traits\NameTrait;

/**
 * Class Character is a player (PC), or non-player (NPC) that exists within the game.
 * @package AdventureGame\Character
 */
class Character
{
    use NameTrait;

    public function __construct(string $name, public ContainerEntityInterface $inventory)
    {
        $this->name = $name;
    }
}
