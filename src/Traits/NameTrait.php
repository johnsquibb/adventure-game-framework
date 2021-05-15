<?php

namespace AdventureGame\Traits;

trait NameTrait
{
    private string $name = '';

    public function getName(): string
    {
        return $this->name;
    }
}