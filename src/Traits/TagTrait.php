<?php

namespace AdventureGame\Traits;

trait TagTrait
{
    private array $tags = [];

    public function getTags(): array
    {
        return $this->tags;
    }

    public function hasTag(string $tag): bool
    {
        foreach ($this->tags as $match) {
            if (strtolower($match) === strtolower($tag)) {
                return true;
            }
        }

        return false;
    }
}
