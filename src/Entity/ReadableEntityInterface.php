<?php

namespace AdventureGame\Entity;

/**
 * Interface ReadableEntityInterface defines methods for entities that are readable.
 * @package AdventureGame\Entity
 */
interface ReadableEntityInterface
{
    public function getReadable(): bool;

    public function setReadable(bool $readable): void;

    public function getLines(): array;
}