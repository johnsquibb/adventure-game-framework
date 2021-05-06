<?php

namespace AdventureGame\Command\Commands;

use AdventureGame\Command\CommandInterface;

class VerbNounCommand implements CommandInterface
{
    public function __construct(private string $verb, private string $noun)
    {
    }

    public function process(): void
    {
        // TODO: Implement process() method.
    }
}