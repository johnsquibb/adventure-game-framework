<?php

namespace AdventureGame\Client;

use AdventureGame\Response\Description;
use AdventureGame\Response\Response;
use AdventureGame\Response\Choice;

/**
 * Class ConsoleResponseDecorator decorates output for display to the player on the console.
 * @package AdventureGame\Client
 */
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

    /**
     * Get the individual output lines to be streamed by the client.
     * @return array
     */
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

        if ($this->response->getChoice() instanceof Choice) {
            array_push($lines, ...$this->renderChoice($this->response->getChoice()));
        }

        $lines[] = $this->blank();

        return $lines;
    }

    /**
     * Render the message lines.
     * @param array $message
     * @return array
     */
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

    /**
     * Render the locations.
     * @param array $locations
     * @return array
     */
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

    /**
     * Render the details about an individual location.
     * @param Description $location
     * @return array
     */
    private function renderLocation(Description $location): array
    {
        $this->response->setClearBefore(true);

        $lines = [];

        if (!empty($location->name)) {
            $lines[] = $this->divider();
            $lines[] = $location->name;
            $lines[] = $this->divider();
            $lines[] = $this->blank();
        }

        if (!empty($location->summary)) {
            $lines[] = $location->summary;
            $lines[] = $this->blank();
        }

        if (!empty($location->description)) {
            $lines[] = $location->description;
            $lines[] = $this->blank();
        }

        return $lines;
    }

    /**
     * Render the items.
     * @param array $items
     * @return array
     */
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

    /**
     * Render the description.
     * @param Description $item
     * @return array
     */
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

    /**
     * Render the containers.
     * @param array $containers
     * @return array
     */
    private function renderContainers(array $containers): array
    {
        $lines = [];

        $lines[] = $this->blank();
        $lines[] = 'You see the following inside:';
        $lines[] = $this->blank();

        foreach ($containers as $description) {
            if ($description instanceof Description) {
                array_push($lines, ...$this->renderDescription($description));
                $lines[] = $this->blank();
            }
        }

        return $lines;
    }

    /**
     * Render the exits.
     * @param array $exits
     * @return array
     */
    private function renderExits(array $exits): array
    {
        $lines = [];

        $lines[] = $this->blank();
        $lines[] = 'You see the following exits:';
        $lines[] = $this->blank();

        foreach ($exits as $description) {
            if ($description instanceof Description) {
                array_push($lines, ...$this->renderDescription($description));
                $lines[] = $this->blank();
            }
        }

        return $lines;
    }

    /**
     * Render a choice.
     * @param Choice $choice
     * @return array
     */
    private function renderChoice(Choice $choice): array
    {
        $lines = [];

        $lines[] = $this->blank();
        $lines[] = $choice->getMessage();

        foreach ($choice->getOptions() as $option) {
            $lines[] = $option;
        }

        return $lines;
    }

    /**
     * Build a visible divider.
     * @return string
     */
    private function divider(): string
    {
        return str_repeat(self::DIVIDER_CHARACTER, self::DIVIDER_WIDTH);
    }

    /**
     * Build a blank character.
     * @return string
     */
    private function blank(): string
    {
        return self::BLANK_CHARACTER;
    }

    /**
     * Build a bullet character.
     * @return string
     */
    private function bullet(): string
    {
        return self::BULLET_CHARACTER;
    }

    /**
     * Build a space character.
     * @return string
     */
    private function space(): string
    {
        return self::SPACE_CHARACTER;
    }

    /**
     * Build a tab character.
     * @return string
     */
    private function tab(): string
    {
        return self::TAB_CHARACTER;
    }
}