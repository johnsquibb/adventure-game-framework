<?php

namespace AdventureGame\Client;

use AdventureGame\Response\Response;

interface ClientControllerInterface
{
    public function getInput(): string;

    public function processResponse(Response $response): void;

    public function setOutput(array $lines): void;
}