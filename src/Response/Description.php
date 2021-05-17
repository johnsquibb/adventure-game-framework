<?php

namespace AdventureGame\Response;

class Description
{
    public function __construct(
        public string $name = '',
        public string $summary = '',
        public string $description = '',
    ) {
    }

    public function getDescription(): string
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

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setSummary(string $summary): void
    {
        $this->summary = $summary;
    }
}