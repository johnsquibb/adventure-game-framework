<?php

namespace AdventureGame\Client;

use AdventureGame\Response\Choice;
use AdventureGame\Response\Description;
use AdventureGame\Response\ItemDescription;
use AdventureGame\Response\Response;

/**
 * Class ConsoleResponseDecorator decorates output for display to the player on the console.
 * @package AdventureGame\Client
 */
class ConsoleResponseDecorator
{
    private const BLANK_CHARACTER = '';
    private const BULLET_CHARACTER = '*';
    private const DIVIDER_CHARACTER = '-';
    private const DIVIDER_WIDTH = 40;
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

        if (!empty($this->response->getLocations())) {
            array_push($lines, ...$this->renderLocations($this->response->getLocations()));
        }

        if (!empty($this->response->getItems())) {
            array_push($lines, ...$this->renderItems($this->response->getItems()));
        }

        if (!empty($this->response->getItemSummaryWithTags())) {
            array_push(
                $lines,
                ...
                $this->renderItemSummariesWithTags($this->response->getItemSummaryWithTags())
            );
        }

        if (!empty($this->response->getInventoryItems())) {
            array_push(
                $lines,
                ...$this->renderInventoryItems($this->response->getInventoryItems())
            );
        }

        if (!empty($this->response->getContainers())) {
            array_push($lines, ...$this->renderContainers($this->response->getContainers()));
        }

        if (!empty($this->response->getExits())) {
            array_push($lines, ...$this->renderExits($this->response->getExits()));
        }

        if (!empty($this->response->getMessages())) {
            array_push($lines, ...$this->renderMessage($this->response->getMessages()));
        }

        if ($this->response->getChoice() instanceof Choice) {
            array_push($lines, ...$this->renderChoice($this->response->getChoice()));
        }

        $lines[] = $this->blank();

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

        if (!empty($location->getName())) {
            $name = $location->getName();
            $lines[] = $this->divider(strlen($name));
            $lines[] = $name;
            $lines[] = $this->divider(strlen($name));
            $lines[] = $this->blank();
        }

        if (!empty($location->getSummary())) {
            $lines[] = $location->getSummary();
            $lines[] = $this->blank();
        }

        if (!empty($location->getDescription())) {
            foreach ($location->getDescription() as $description) {
                $lines[] = $description;
            }
            $lines[] = $this->blank();
        }

        return $lines;
    }

    /**
     * Build a divider of defined width.
     * @param int $width
     * @return string
     */
    private function divider(int $width = self::DIVIDER_WIDTH): string
    {
        return str_repeat(self::DIVIDER_CHARACTER, $width);
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
     * Render the items.
     * @param array $items
     * @return array
     */
    private function renderItems(array $items): array
    {
        $lines = [$this->blank()];

        $heading = 'Items';
        $lines[] = $heading;
        $lines[] = $this->divider(strlen($heading));
        $lines[] = $this->blank();

        foreach ($items as $description) {
            if ($description instanceof ItemDescription) {
                array_push($lines, ...$this->renderItemDescription($description));
                $lines[] = $this->blank();
            }
        }

        return $lines;
    }

    /**
     * Render the item description.
     * @param ItemDescription $item
     * @return array
     */
    private function renderItemDescription(ItemDescription $item): array
    {
        $lines = [];

        if (!empty($item->name)) {
            $name = $this->bullet() . $this->space() . $item->name;

            $status = $item->getStatus();
            if (!empty($status)) {
                $name .= $this->space() . "($status)";
            }

            $lines[] = $name;
        }

        if (!empty($item->getSummary())) {
            $lines[] = $this->tab() . $item->getSummary();
        }

        if (!empty($item->getDescription())) {
            foreach ($item->getDescription() as $description) {
                $lines[] = $this->tab() . $description;
            }
        }

        return $lines;
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

    /**
     * Draw a heading with underline.
     * @param string $text
     * @return array
     */
    private function heading(string $text): array
    {
        return [
            $this->blank(),
            $text,
            $this->divider(strlen($text)),
            $this->blank()
        ];
    }

    /**
     * Render the item summaries with suggested tags.
     * @param array $items
     * @return array
     */
    private function renderItemSummariesWithTags(array $items): array
    {
        $lines = [];

        $lines[] = $this->blank();
        $lines[] = "You see:";
        $lines[] = $this->blank();

        foreach ($items as $description) {
            if ($description instanceof ItemDescription) {
                array_push($lines, ...$this->renderItemSummaryWithTag($description));
                $lines[] = $this->blank();
            }
        }

        return $lines;
    }

    /**
     * Render the summary with tag.
     * @param ItemDescription $item
     * @return array
     */
    private function renderItemSummaryWithTag(ItemDescription $item): array
    {
        $lines = [];

        if (!empty($item->name)) {
            $tag = $item->getTags()[0] ?? '';
            $lines[] = $this->bullet() . $this->space() . $item->name . $this->tab() . "[$tag]";
        }

        if (!empty($item->summary)) {
            $lines[] = $this->tab() . $item->summary;
        }

        return $lines;
    }

    /**
     * Render the inventory items.
     * @param array $items
     * @return array
     */
    private function renderInventoryItems(array $items): array
    {
        $lines = $this->heading('Inventory');

        foreach ($items as $description) {
            if ($description instanceof ItemDescription) {
                array_push($lines, ...$this->renderItemDescription($description));
                $lines[] = $this->blank();
            }
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
        $lines = $this->heading('Contents');

        foreach ($containers as $description) {
            if ($description instanceof Description) {
                array_push($lines, ...$this->renderDescription($description));
                $lines[] = $this->blank();
            }
        }

        return $lines;
    }

    /**
     * Render the description for entity.
     * @param Description $entity
     * @return array
     */
    private function renderDescription(Description $entity): array
    {
        $lines = [];

        if (!empty($entity->getName())) {
            $lines[] = $this->bullet() . $this->space() . $entity->getName();
        }

        if (!empty($entity->getSummary())) {
            $lines[] = $this->tab() . $entity->getSummary();
        }

        if (!empty($entity->getDescription())) {
            foreach ($entity->getDescription() as $description) {
                $lines[] = $this->tab() . $description;
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
        $lines = $this->heading('Exits');

        foreach ($exits as $description) {
            if ($description instanceof Description) {
                array_push($lines, ...$this->renderDescription($description));
                $lines[] = $this->blank();
            }
        }

        return $lines;
    }

    /**
     * Render the message lines.
     * @param array $message
     * @return array
     */
    private function renderMessage(array $message): array
    {
        $lines = [$this->blank()];

        if (!empty($message)) {
            foreach ($message as $line) {
                $lines[] = $line;
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
}