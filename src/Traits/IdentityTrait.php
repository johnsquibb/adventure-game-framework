<?php

namespace AdventureGame\Traits;

trait IdentityTrait
{
    private string $id = '';

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }
}