<?php

namespace AdventureGame\IO;

class OutputController
{
    private array $lines = [];

    public function addLine(string $line): void
    {
        $this->lines[] = $line;
    }

    public function addLines(array $lines): void
    {
        foreach ($lines as $line) {
            $this->addLine($line);
        }
    }
}