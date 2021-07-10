<?php

namespace AdventureGame\Traits;

trait DescriptionTrait
{
    private array $description = [];
    private string $summary = '';

    public function getDescription(): array
    {
        return $this->description;
    }

    public function getSummary(): string
    {
        return $this->summary;
    }
}
