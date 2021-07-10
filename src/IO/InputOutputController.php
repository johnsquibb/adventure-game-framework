<?php

namespace AdventureGame\IO;

/**
 * Class InputOutputController provides methods for text input and output.
 * @package AdventureGame\IO
 */
abstract class InputOutputController
{
    protected array $lines = [];

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
     * Add a line.
     * @param string $line
     */
    public function addLine(string $line): void
    {
        $this->lines[] = $line;
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

    /**
     * Get lines.
     * @return array
     */
    public function getLines(): array
    {
        return $this->lines;
    }

    /**
     * Clear lines.
     * @return void
     */
    public function clearLines(): void
    {
        $this->lines = [];
    }
}
