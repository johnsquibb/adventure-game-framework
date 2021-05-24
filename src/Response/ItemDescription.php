<?php

namespace AdventureGame\Response;

class ItemDescription extends Description
{
    public function __construct(
        string $name = '',
        string $summary = '',
        string $description = '',
        public array $tags = [],
    ) {
        parent::__construct($name, $summary, $description);
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}