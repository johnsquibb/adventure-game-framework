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
            $input = $clientController->getInput();
            $this->platformRegistry->inputController->processInput($input);
            $lines = $this->platformRegistry->outputController->getLinesAndClear();
            var_dump($lines);exit;
            exit;
        }
    }
}