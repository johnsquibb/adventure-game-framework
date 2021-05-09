<?php

namespace AdventureGame\Client;

use AdventureGame\Client\Terminal\TerminalIO;

class ConsoleClientController implements ClientControllerInterface
{
    private TerminalIO $terminal;

    public function __construct()
    {
        $this->terminal = new TerminalIO();
    }

    public function getInput(): string
    {
        // Try: `take test-item-in-container from test-container-item`
        return $this->terminal->read();
    }

    public function setOutput(array $lines): void
    {
        foreach ($lines as $line) {
            $this->terminal->writeLine($line);
        }
    }
}