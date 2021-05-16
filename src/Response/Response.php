<?php

namespace AdventureGame\Response;

class Response
{
    public string $title = '';
    public string $heading = '';
    public string $message = '';
    public array $items = [];
    public array $containers = [];
    public array $exits = [];

    public function addItemDescription(Description $item): void
    {
        $this->items[] = $item;
    }

    public function addContainerDescription(Description $container): void
    {
        $this->containers[] = $container;
    }

    public function addExitDescription(Description $exit): void
    {
        $this->exits[] = $exit;
    }
}