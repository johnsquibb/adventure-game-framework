<?php

namespace AdventureGame\Traits;

/**
 * Trait ReadableTrait provides methods for objects implementing ReadableInterface.
 * @package AdventureGame\Traits
 */
trait ReadableTrait
{
    private array $lines = [];
    private bool $readable = false;

    public function addLine(string $line): void
    {
        $this->lines[] = $line;
    }

    public function getLines(): array
    {
        return $this->lines;
    }

    public function setLines(array $lines): void
    {
        $this->lines = $lines;
    }

    public function getReadable(): bool
    {
        return $this->readable;
    }

    public function setReadable(bool $readable): void
    {
        $this->readable = $readable;
    }
}
