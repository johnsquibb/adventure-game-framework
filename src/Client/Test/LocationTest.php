<?php

namespace AdventureGame\Client\Test;

use Exception;

class LocationTest implements TestInterface
{
    public function __construct(
        private string $input,
        private string $expectedLocationId,
    ) {
    }

    public function getInput(): string
    {
        return $this->input;
    }

    /**
     * Assert location matches expected test value.
     * @param string $locationId
     * @throws Exception
     */
    public function assertExpectedLocationId(string $locationId): void
    {
        if ($locationId !== $this->expectedLocationId) {
            throw new Exception(
                sprintf(
                    'ERROR: want location "%s", got "%s"',
                    $this->expectedLocationId,
                    $locationId
                )
            );
        }
    }
}
