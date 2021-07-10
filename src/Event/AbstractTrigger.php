<?php

namespace AdventureGame\Event;

abstract class AbstractTrigger implements TriggerInterface
{
    protected string $id = '';

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }
}
