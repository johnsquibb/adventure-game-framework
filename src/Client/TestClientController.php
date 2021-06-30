<?php

namespace AdventureGame\Client;

use AdventureGame\Client\Terminal\TerminalIO;
use AdventureGame\Client\Test\InventoryTest;
use AdventureGame\Client\Test\LocationTest;
use AdventureGame\Client\Test\TestInterface;
use AdventureGame\Item\ItemInterface;
use AdventureGame\Platform\PlatformRegistry;
use AdventureGame\Response\Choice;
use AdventureGame\Response\Response;
use Exception;

/**
 * Class ConsoleClientController takes user input and renders game output using the console.
 * @package AdventureGame\Client
 */
class TestClientController implements ClientControllerInterface
{
    const MESSAGE_TESTS_COMPLETE = 'Tests complete.';
    const TEST_DELAY_MILLISECONDS = 2000;

    private TerminalIO $terminal;
    private array $inputHistory = [];

    // How long to wait between each test.
    private $waitTimeMilliseconds = self::TEST_DELAY_MILLISECONDS;

    // Game controller runs a command to show the spawn location details, offset to accommodate.
    private int $counter = -1;

    public function __construct(private PlatformRegistry $platformRegistry, private array $tests)
    {
        $this->terminal = new TerminalIO();
        $this->terminal->clear();
    }

    /**
     * Process game response objects.
     * @param Response $response
     */
    public function processResponse(Response $response): void
    {
        if ($response->getChoice()) {
            $this->handleChoice($response);
            return;
        }

        $this->streamResponseLines($response);

        $this->checkTest();
        $this->counter++;
    }

    /**
     * Handle game response object choice.
     * @param Response $response
     */
    private function handleChoice(Response $response): void
    {
        $choice = $response->getChoice();
        if ($choice instanceof Choice) {
            $decorator = new ConsoleResponseDecorator($response);
            $lines = $decorator->getLines();
            $this->setOutput($lines);

            $input = $this->getInput();
            $choice->invoke(['answer' => $input]);
        }
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
     * Get user input from console.
     * @return string
     * @throws TestsCompleteException
     */
    public function getInput(): string
    {
        $test = $this->getTest();
        if ($test === null) {
            throw new TestsCompleteException(self::MESSAGE_TESTS_COMPLETE);
        }

        $input = $test->getInput();
        $this->inputHistory[] = $input;
        $this->streamMessage('TEST INPUT: ' . $input);

        $this->wait();

        return $input;
    }

    public function setWaitTimeMilliseconds(int $waitTimeMilliseconds): void
    {
        $this->waitTimeMilliseconds = $waitTimeMilliseconds;
    }

    private function wait(): void
    {
        usleep($this->waitTimeMilliseconds * 1000);
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

    private function streamMessage(string $message): void
    {
        $response = new Response();
        $response->addMessage($message);
        $this->streamResponseLines($response);
    }

    private function streamMessages(array $messages): void
    {
        foreach ($messages as $message) {
            $this->streamMessage($message);
        }
    }

    private function getTest(): ?TestInterface
    {
        return $this->tests[$this->counter] ?? null;
    }

    private function checkTest()
    {
        $test = $this->getTest();
        if ($test instanceof LocationTest) {
            $this->checkMovementTest($test);
        }

        if ($test instanceof InventoryTest) {
            $this->checkInventoryTest($test);
        }
    }

    /**
     * Display and throw test error.
     * @param string $message
     * @throws TestErrorException
     */
    private function errorTest(string $message): void
    {
        $this->streamMessages(
            [
                $message,
                sprintf('TEST INDEX: %d', $this->counter)
            ]
        );

        throw new TestErrorException('');
    }

    private function checkMovementTest(LocationTest $test)
    {
        try {
            $test->assertExpectedLocationId(
                $this->platformRegistry->mapController->getPlayerLocation()->getId()
            );
        } catch (Exception $e) {
            $this->errorTest($e->getMessage());
        }
    }

    private function checkInventoryTest(InventoryTest $test)
    {
        $inventory = $this->platformRegistry->playerController->getPlayerInventory();
        $itemIds = [];
        foreach ($inventory->getItems() as $item) {
            if ($item instanceof ItemInterface) {
                $itemIds[] = $item->getId();
            }
        }

        try {
            $test->assertExpectedItems($itemIds);
        } catch (Exception $e) {
            $this->errorTest($e->getMessage());
        }
    }
}