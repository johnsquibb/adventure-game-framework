<?php

namespace AdventureGame\IO;

interface InputOutputControllerInterface
{
    /**
     * Add multiple lines.
     * @param array $lines
     */
    public function addLines(array $lines): void;

    /**
     * Add a line.
     * @param string $line
     */
    public function addLine(string $line): void;
    /**
     * Get lines and clear.
     * @return array
     */
    public function getLinesAndClear(): array;

    /**
     * Get lines.
     * @return array
     */
    public function getLines(): array;

    /**
     * Clear lines.
     * @return void
     */
    public function clearLines(): void;
}