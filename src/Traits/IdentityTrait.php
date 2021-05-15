<?php

namespace AdventureGame\Traits;

trait IdentityTrait
{
    private string $id = '';

    public function getId(): string
    {
        return $this->id;
    }
}