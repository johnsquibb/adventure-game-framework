<?php

namespace AdventureGame\Client;

use AdventureGame\Client\Terminal\TerminalIO;

class ConsoleClientController implements ClientControllerInterface
{
    private TerminalIO $terminal;

    public function __construct()
    {
        $this->terminal = new TerminalIO();
        $this->terminal->clear();
    }

    public function getInput(): string
    {
        return $this->terminal->read();
    }

    public function setOutput(array $lines): void
    {
        foreach ($lines as $line) {
            $this->terminal->writeLine($line);
        }
    }
}