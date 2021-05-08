<?php

namespace AdventureGame\Client;

class ConsoleClientController implements ClientControllerInterface
{
    public function getInput(): string
    {
        // Testing 1..2..5!
        return 'take test-item-in-container from test-container-item';
    }
}