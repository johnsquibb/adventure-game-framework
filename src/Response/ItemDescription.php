<?php

namespace AdventureGame\Response;

class ItemDescription extends Description
{
    private string $status = '';

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

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }
}