<?php

namespace AdventureGame\Platform;

use AdventureGame\Client\ClientControllerInterface;

class PlatformController
{
    public function __construct(
        public PlatformRegistry $platformRegistry,
    ) {
    }

    public function run(ClientControllerInterface $clientController): void {
        while (true) {
            $clientController->getInput();
        }
    }
}