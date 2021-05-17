<?php

namespace AdventureGame\Client;

use AdventureGame\Response\Description;
use AdventureGame\Response\Response;

class ConsoleResponseDecorator
{
    private const DIVIDER_WIDTH = 40;

    private const BLANK_CHARACTER = '';
    private const DIVIDER_CHARACTER = '-';
    private const BULLET_CHARACTER = '*';
    private const SPACE_CHARACTER = ' ';
    private const TAB_CHARACTER = '    ';

    public function __construct(private Response $response)
    {
    }

    public function getLines(): array
    {
        $lines = [];

        if (!empty($this->response->getMessage())) {
            array_push($lines, ...$this->renderMessage($this->response->getMessage()));
        }

        if (!empty($this->response->getLocations())) {
            array_push($lines, ...$this->renderLocations($this->response->getLocations()));
        }

        if (!empty($this->response->getItems())) {
            array_push($lines, ...$this->renderItems($this->response->getItems()));
        }

        if (!empty($this->response->getContainers())) {
            array_push($lines, ...$this->renderContainers($this->response->getContainers()));
        }

        if (!empty($this->response->getExits())) {
            array_push($lines, ...$this->renderExits($this->response->getExits()));
        }

        $lines[] = $this->blank();

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
            $lines[] = $this->blank();
        }


        if (!empty($item->summary)) {
            $lines[] = $item->summary;
            $lines[] = $this->blank();
        }

        if (!empty($item->description)) {
            $lines[] = $item->description;
            $lines[] = $this->blank();
        }

        return $lines;
    }

    private function divider(): string
    {
        return str_repeat(self::DIVIDER_CHARACTER, self::DIVIDER_WIDTH);
    }

    private function blank(): string
    {
        return self::BLANK_CHARACTER;
    }

    private function bullet(): string
    {
        return self::BULLET_CHARACTER;
    }

    private function space(): string
    {
        return self::SPACE_CHARACTER;
    }

    private function tab(): string
    {
        return self::TAB_CHARACTER;
    }

    private function renderItems(array $items): array
    {
        $lines = [];

        $lines[] = $this->blank();
        $lines[] = "You see:";
        $lines[] = $this->blank();

        foreach ($items as $description) {
            if ($description instanceof Description) {
                array_push($lines, ...$this->renderDescription($description));
                $lines[] = $this->blank();
            }
        }

        return $lines;
    }

    private function renderDescription(Description $item): array
    {
        $lines = [];

        if (!empty($item->name)) {
            $lines[] = $this->bullet() . $this->space() . $item->name;
        }

        if (!empty($item->summary)) {
            $lines[] = $this->tab() . $item->summary;
        }

        if (!empty($item->description)) {
            $lines[] = $this->tab() . $item->description;
        }

        return $lines;
    }

    private function renderContainers(array $containers): array
    {
        $lines = [];

        $lines[] = $this->blank();
        $lines[] = "You see the following inside:";
        $lines[] = $this->blank();

        foreach ($containers as $description) {
            if ($description instanceof Description) {
                array_push($lines, ...$this->renderDescription($description));
                $lines[] = $this->blank();
            }
        }

        return $lines;
    }

    private function renderExits(array $exits): array
    {
        $lines = [];

        $lines[] = $this->blank();
        $lines[] = "You see the following exits:";
        $lines[] = $this->blank();

        foreach ($exits as $description) {
            if ($description instanceof Description) {
                array_push($lines, ...$this->renderDescription($description));
                $lines[] = $this->blank();
            }
        }

        return $lines;
    }
}