<?php

namespace AdventureGame\Command\Commands;

use AdventureGame\Command\CommandInterface;

class VerbCommand implements CommandInterface
{
    public function __construct(private string $verb)
    {
    }

    public function process(): void
    {
        switch ($this->verb) {
            case 'take':

        }
    }
}