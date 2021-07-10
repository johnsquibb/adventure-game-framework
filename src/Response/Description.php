<?php

namespace AdventureGame\Response;

class Description
{
    public function __construct(
        public string $name = '',
        public string $summary = '',
        public array $description = [],
    ) {
    }

    public function getDescription(): array
    {
        return $this->description;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSummary(): string
    {
        return $this->summary;
    }
}
