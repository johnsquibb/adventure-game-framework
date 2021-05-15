<?php

namespace AdventureGame\Item;

trait NameTrait
{
    private string $name = '';

    public function getName(): string
    {
        return $this->name;
    }
}