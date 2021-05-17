<?php

namespace AdventureGame\Platform;

use AdventureGame\Client\ClientControllerInterface;
use AdventureGame\Command\Exception\InvalidCommandException;
use AdventureGame\Command\Exception\InvalidTokenException;
use AdventureGame\Game\Exception\InvalidExitException;
use AdventureGame\Response\Response;

class PlatformController
{
    public function __construct(
        private PlatformRegistry $platformRegistry
    ) {
    }

    public function run(ClientControllerInterface $clientController): void
    {
        // On game load, show the current location.
        $response = $this->processInput('look');
        $clientController->setResponse($response);

        for (; ;) {
            $input = $clientController->getInput();
            if (!empty($input)) {
                $response = $this->processInput($input);
                $clientController->setResponse($response);
            }
        }
    }

    private function processInput(string $input): Response
    {
        try {
            $response = $this->platformRegistry->inputController->processInput($input);

            if ($response === null) {
                return $this->noCommandProcessedMessage();
            }

            return $response;
        } catch (InvalidCommandException | InvalidTokenException) {
            return $this->invalidCommandMessage();
        } catch (InvalidExitException) {
            return $this->invalidExitMessage();
        }
    }

    private function noCommandProcessedMessage(): Response
    {
        $response = new Response();

        $response->addMessage("You can't do that.");
        return $response;
    }

    private function invalidCommandMessage(): Response
    {
        $response = new Response();

        $response->addMessage("That can't be done here.");
        return $response;
    }

    private function invalidExitMessage(): Response
    {
        $response = new Response();

        $response->addMessage("Unable to go that way.");
        return $response;
    }
}