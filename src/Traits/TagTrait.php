<?php

namespace AdventureGame\Traits;

trait TagTrait
{
    private string $tag = '';

    public function getTag(): string
    {
        return $this->tag;
    }
}