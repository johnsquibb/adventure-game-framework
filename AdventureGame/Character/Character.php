<?php

namespace AdventureGame\Character;

use AdventureGame\Item\ContainerInterface;

class Character
{
    public function __construct(public string $name, public ContainerInterface $inventory)
    {
    }
}