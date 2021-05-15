<?php

namespace AdventureGame\Item;

trait TagTrait
{
    private string $tag = '';

    public function getTag(): string
    {
        return $this->tag;
    }
}