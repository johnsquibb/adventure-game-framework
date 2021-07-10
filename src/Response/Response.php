<?php

namespace AdventureGame\Response;

/**
 * Class Response is a transport for displaying game output to the player.
 * @package AdventureGame\Response
 */
class Response
{
    private array $messages = [];
    private array $items = [];
    private array $itemSummaryWithTags = [];
    private array $inventoryItems = [];
    private array $containers = [];
    private array $locations = [];
    private array $exits = [];
    private bool $clearBefore = false;
    private ?Choice $choice = null;

    public function addContainerDescription(Description $container): void
    {
        $this->containers[] = $container;
    }

    public function addExitDescription(Description $exit): void
    {
        $this->exits[] = $exit;
    }

    public function addInventoryItemDescription(ItemDescription $inventoryItem): void
    {
        $this->inventoryItems[] = $inventoryItem;
    }

    public function addItemDescription(ItemDescription $item): void
    {
        $this->items[] = $item;
    }

    public function addItemSummaryWithTag(ItemDescription $itemSummaryWithTag): void
    {
        $this->itemSummaryWithTags[] = $itemSummaryWithTag;
    }

    public function addLocationDescription(Description $location): void
    {
        $this->locations[] = $location;
    }

    public function addMessages(array $messages): void
    {
        foreach ($messages as $message) {
            $this->addMessage($message);
        }
    }

    public function addMessage(string $message): void
    {
        $this->messages[] = $message;
    }

    public function getChoice(): ?Choice
    {
        return $this->choice;
    }

    public function setChoice(Choice $choice): void
    {
        $this->choice = $choice;
    }

    public function getClearBefore(): bool
    {
        return $this->clearBefore;
    }

    public function setClearBefore(bool $clearBefore): void
    {
        $this->clearBefore = $clearBefore;
    }

    public function getContainers(): array
    {
        return $this->containers;
    }

    public function getExits(): array
    {
        return $this->exits;
    }

    public function getInventoryItems(): array
    {
        return $this->inventoryItems;
    }

    public function getItemSummaryWithTags(): array
    {
        return $this->itemSummaryWithTags;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getLocations(): array
    {
        return $this->locations;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }
}
