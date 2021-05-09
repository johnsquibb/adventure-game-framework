<?php

namespace AdventureGame\Client;

interface ClientControllerInterface
{
    public function getInput(): string;
    public function setOutput(array $lines): void;
}