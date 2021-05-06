<?php

namespace AdventureGame\Command\Commands;

use AdventureGame\Command\CommandInterface;

class VerbPrepositionNounCommand implements CommandInterface
{
    public function __construct(
        private string $verb,
        private string $preposition,
        private string $noun,
    ) {
    }

    public function process(): void
    {
        // TODO: Implement process() method.
    }
}