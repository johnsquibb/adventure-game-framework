<?php

namespace AdventureGame\Client;

use AdventureGame\Response\Description;
use AdventureGame\Response\Response;

class ConsoleResponseDecorator
{
    private const DIVIDER_CHARACTER = '-';
    private const DIVIDER_WIDTH = 40;

    public function __construct(private Response $response)
    {
    }

    public function getLines(): array
    {
        $lines = [];

        array_push($lines, ...$this->renderMessage($this->response->getMessage()));
        array_push($lines, ...$this->renderLocations($this->response->getLocations()));
        array_push($lines, ...$this->renderItems($this->response->getItems()));
        array_push($lines, ...$this->renderContainers($this->response->getContainers()));
        array_push($lines, ...$this->renderExits($this->response->getExits()));

        return $lines;
    }

    private function renderMessage(array $message): array
    {
        $lines = [];

        if (!empty($message)) {
            foreach ($message as $line) {
                $lines[] = $line;
            }
        }

        return $lines;
    }

    private function renderLocations(array $locations): array
    {
        $lines = [];

        foreach ($locations as $description) {
            if ($description instanceof Description) {
                array_push($lines, ...$this->renderLocation($description));
            }
        }

        return $lines;
    }

    private function renderLocation(Description $item): array
    {
        $this->response->setClearBefore(true);

        $lines = [];

        if (!empty($item->name)) {
            $lines[] = $this->divider();
            $lines[] = $item->name;
            $lines[] = $this->divider();
            $lines[] = $this->blankLine();
        }


        if (!empty($item->summary)) {
            $lines[] = $item->summary;
            $lines[] = $this->blankLine();
        }

        if (!empty($item->description)) {
            $lines[] = $item->description;
            $lines[] = $this->blankLine();
        }

        return $lines;
    }

    private function divider(): string
    {
        return str_repeat(self::DIVIDER_CHARACTER, self::DIVIDER_WIDTH);
    }

    private function blankLine(): string
    {
        return '';
    }

    private function renderItems(array $items): array
    {
        $lines = [];

        foreach ($items as $description) {
            if ($description instanceof Description) {
                array_push($lines, ...$this->renderDescription($description));
            }
        }

        return $lines;
    }

    private function renderDescription(Description $item): array
    {
        $lines = [];

        if (!empty($item->name)) {
            $lines[] = $item->name;
        }

        if (!empty($item->summary)) {
            $lines[] = $item->summary;
        }

        if (!empty($item->description)) {
            $lines[] = $item->description;
        }

        return $lines;
    }

    private function renderContainers(array $containers): array
    {
        $lines = [];

        foreach ($containers as $description) {
            if ($description instanceof Description) {
                array_push($lines, ...$this->renderDescription($description));
            }
        }

        return $lines;
    }

    private function renderExits(array $exits): array
    {
        $lines = [];

        foreach ($exits as $description) {
            if ($description instanceof Description) {
                array_push($lines, ...$this->renderDescription($description));
            }
        }

        return $lines;
    }
}