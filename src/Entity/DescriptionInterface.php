<?php

namespace AdventureGame\Entity;

interface DescriptionInterface
{
    public function getDescription(): string;

    public function getSummary(): string;
}