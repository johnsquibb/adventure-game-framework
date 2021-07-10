<?php

namespace AdventureGame\IO;

interface InputOutputControllerInterface
{
    /**
     * Add a line.
     * @param string $line
     */
    public function addLine(string $line): void;

    /**
     * Add multiple lines.
     * @param array $lines
     */
    public function addLines(array $lines): void;

    /**
     * Clear lines.
     * @return void
     */
    public function clearLines(): void;

    /**
     * Get lines.
     * @return array
     */
    public function getLines(): array;

    /**
     * Get lines and clear.
     * @return array
     */
    public function getLinesAndClear(): array;
}
