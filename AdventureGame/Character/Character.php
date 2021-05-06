<?php

namespace AdventureGame\Character;

use AdventureGame\Item\Container;

class Character
{
    public function __construct(public string $name, public Container $inventory)
    {
    }
}