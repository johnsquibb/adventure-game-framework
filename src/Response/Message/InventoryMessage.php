<?php

namespace AdventureGame\Response\Message;

use AdventureGame\Response\MessageInterface;

class InventoryMessage implements MessageInterface
{
    public const TYPE_INVENTORY_EMPTY = 'empty';

    public function __construct(private string $messageType)
    {
    }

    public function toString(): string
    {
        return match ($this->messageType) {
            self::TYPE_INVENTORY_EMPTY => "You aren't carrying anything",
            default => '',
        };
    }
}