<?php

namespace AdventureGame\Item;

trait DescriptionTrait
{
    private string $description = '';

    public function getDescription(): string
    {
        return $this->description;
    }
}