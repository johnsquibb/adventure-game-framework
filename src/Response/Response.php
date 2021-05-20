<?php

namespace AdventureGame\Response;

/**
 * Class Response is a transport for displaying game output to the player.
 * @package AdventureGame\Response
 */
class Response
{
    private array $message = [];
    private array $items = [];
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

    public function addItemDescription(Description $item): void
    {
        $this->items[] = $item;
    }

    public function addLocationDescription(Description $location): void
    {
        $this->locations[] = $location;
    }

    public function addMessage(string $message): void
    {
        $this->message[] = $message;
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

    public function getItems(): array
    {
        return $this->items;
    }

    public function getLocations(): array
    {
        return $this->locations;
    }

    public function getMessage(): array
    {
        return $this->message;
    }

    public function getChoice(): ?Choice
    {
        return $this->choice;
    }

    public function setChoice(Choice $choice): void
    {
        $this->choice = $choice;
    }
}