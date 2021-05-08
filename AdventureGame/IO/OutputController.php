<?php

namespace AdventureGame\IO;

class OutputController
{
    private array $lines = [];

    /**
     * Add a line.
     * @param string $line
     */
    public function addLine(string $line): void
    {
        $this->lines[] = $line;
    }

    /**
     * Add multiple lines.
     * @param array $lines
     */
    public function addLines(array $lines): void
    {
        foreach ($lines as $line) {
            $this->addLine($line);
        }
    }

    /**
     * Clear lines.
     * @return void
     */
    public function clearLines(): void
    {
        $this->lines = [];
    }

    /**
     * Get lines.
     * @return array
     */
    public function getLines(): array
    {
        return $this->lines;
    }

    /**
     * Get lines and clear.
     * @return array
     */
    public function getLinesAndClear(): array
    {
        $lines = $this->getLines();
        $this->clearLines();

        return $lines;
    }
}