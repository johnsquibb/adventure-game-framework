<?php

namespace AdventureGame\Entity;

interface TaggableEntityInterface extends EntityInterface
{
    public function getTags(): array;

    public function hasTag(string $tag): bool;
}