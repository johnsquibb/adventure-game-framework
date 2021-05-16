<?php

namespace AdventureGame\Entity;

interface TaggableEntityInterface extends EntityInterface
{
    public function getTag(): string;
}