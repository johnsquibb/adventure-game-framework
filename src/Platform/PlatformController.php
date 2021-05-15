<?php

namespace AdventureGame\Platform;

use AdventureGame\Client\ClientControllerInterface;
use AdventureGame\Command\Exception\InvalidCommandException;
use AdventureGame\Game\Exception\InvalidExitException;

class PlatformController
{
    public function __construct(
        public PlatformRegistry $platformRegistry,
    ) {
    }

    public function run(ClientControllerInterface $clientController): void
    {
        for (; ;) {
            $input = $clientController->getInput();

            $lines = $this->processInput($input);

            $clientController->setOutput($lines);
        }
    }

    private function processInput(string $input): array
    {
        try {
            $result = $this->platformRegistry->inputController->processInput($input);
            if ($result === false) {
                return $this->noCommandProcessedMessage();
            }
            return $this->platformRegistry->outputController->getLinesAndClear();
        } catch (InvalidCommandException $e) {
            return $this->invalidCommandMessage();
        } catch (InvalidExitException $e) {
            return $this->invalidExitMessage();
        }
    }

    private function noCommandProcessedMessage(): array
    {
        return [
            "Can't do that.",
        ];
    }

    private function invalidCommandMessage(): array
    {
        return [
            "Can't do that here.",
        ];
    }

    private function invalidExitMessage(): array
    {
        return [
            "Can't go that way.",
        ];
    }
}