<?php

namespace AdventureGame\Command\Commands;

use AdventureGame\Command\CommandInterface;

class VerbNounPrepositionNounCommand implements CommandInterface
{
    public function __construct(
        private string $verb,
        private string $noun1,
        private string $preposition,
        private string $noun2,
    ) {
    }

    public function process(): void
    {
        // TODO: Implement process() method.
    }
}