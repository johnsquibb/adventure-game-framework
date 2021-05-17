<?php

namespace AdventureGame\Client;

use AdventureGame\Response\Response;

interface ClientControllerInterface
{
    public function getInput(): string;

    public function setOutput(array $lines): void;

    public function setResponse(Response $response): void;
}