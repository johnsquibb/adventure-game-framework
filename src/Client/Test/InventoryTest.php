<?php

namespace AdventureGame\Client\Test;

use Exception;

class InventoryTest implements TestInterface
{
    public function __construct(
        private string $input,
        private array $expectedItemIds
    ) {
    }

    public function getInput(): string
    {
        return $this->input;
    }

    /**
     * Assert all test items are present in items list.
     * @param array $itemIds
     * @throws Exception
     */
    public function assertExpectedItems(array $itemIds): void
    {
        sort($itemIds);
        sort($this->expectedItemIds);

        if ($this->expectedItemIds !== $itemIds) {
            throw new Exception(
                sprintf(
                    'ERROR: want items "%s", got "%s"',
                    implode(',', $this->expectedItemIds),
                    implode(',', $itemIds)
                )
            );
        }
    }
}