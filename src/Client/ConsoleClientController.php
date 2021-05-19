<?php

namespace AdventureGame\Client;

use AdventureGame\Client\Terminal\TerminalIO;
use AdventureGame\Response\Response;
use AdventureGame\Response\Trigger;

/**
 * Class ConsoleClientController takes user input and renders game output using the console.
 * @package AdventureGame\Client
 */
class ConsoleClientController implements ClientControllerInterface
{
    private TerminalIO $terminal;

    public function __construct()
    {
        $this->terminal = new TerminalIO();
        $this->terminal->clear();
    }

    /**
     * Get user input from console.
     * @return string
     */
    public function getInput(): string
    {
        return $this->terminal->read();
    }

    /**
     * Process game response objects.
     * @param Response $response
     */
    public function processResponse(Response $response): void
    {
        if ($response->getTrigger()) {
            $this->handleTrigger($response);
            return;
        }

        $this->streamResponseLines($response);
    }

    /**
     * Stream game response object lines to console.
     * @param Response $response
     */
    private function streamResponseLines(Response $response): void
    {
        $decorator = new ConsoleResponseDecorator($response);
        $lines = $decorator->getLines();

        if ($response->getClearBefore()) {
            $this->terminal->clear();
        }

        $this->setOutput($lines);
    }

    /**
     * Display content on the console.
     * @param array $lines
     */
    public function setOutput(array $lines): void
    {
        foreach ($lines as $line) {
            $this->terminal->writeLine($line);
        }
    }

    /**
     * Handle game response object trigger.
     * @param Response $response
     */
    private function handleTrigger(Response $response): void
    {
        $trigger = $response->getTrigger();
        if ($trigger instanceof Trigger) {
            $decorator = new ConsoleResponseDecorator($response);
            $lines = $decorator->getLines();
            $this->setOutput($lines);

            $input = $this->getInput();
            $trigger->invoke(['answer' => $input]);
        }
    }
}