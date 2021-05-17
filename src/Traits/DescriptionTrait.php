<?php

namespace AdventureGame\Traits;

trait DescriptionTrait
{
    private string $description = '';
    private string $summary = '';

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getSummary(): string
    {
        return $this->summary;
    }
}