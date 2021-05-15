<?php

namespace AdventureGame\Item;

trait IdentityTrait
{
    private string $id = '';

    public function getId(): string
    {
        return $this->id;
    }
}