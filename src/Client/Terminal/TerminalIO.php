<?php

namespace AdventureGame\Client\Terminal;

/**
 * Class TerminalIO provides methods for working with terminal applications.
 * @package AdventureGame\Client\Terminal
 */
final class TerminalIO
{
    private const INPUT_PROMPT = '> ';

    public function clear(): void
    {
        // http://pank.org/blog/2011/02/php-clear-terminal-screen.html
        // Works for various terms, doesn't require readline/libedit support or ncurses.
        echo chr(27) . chr(91) . 'H' . chr(27) . chr(91) . 'J';
    }

    public function read(): string
    {
        $input = readline(self::INPUT_PROMPT);
        $trimmed = trim($input);

        if ($trimmed) {
            $this->addHistory($input);
        }

        return $trimmed;
    }

    private function addHistory(string $entry): void
    {
        readline_add_history($entry);
    }

    public function usage(string $line): void
    {
        $this->writeLine("Usage: {$line}");
    }

    public function writeLine(string $line): void
    {
        $this->write($line);
        $this->write(PHP_EOL);
    }

    public function write(string $string): void
    {
        echo $string;
    }

    public function waitForAnyInput(): void
    {
        readline();
    }

    public function warn(string $line): void
    {
        $this->writeLine("Warn: {$line}");
    }
}