<?php

namespace AdventureGame\Response\Message;

use AdventureGame\Response\MessageInterface;

/**
 * Class InventoryMessage builds messages pertaining to the player inventory.
 * @package AdventureGame\Response\Message
 */
class InventoryMessage implements MessageInterface
{
    public const TYPE_INVENTORY_EMPTY = 'inventory-empty';
    public const TYPE_INVENTORY_FULL = 'inventory-full';

    public function __construct(private string $messageType)
    {
    }

    public function toString(): string
    {
        return match ($this->messageType) {
            self::TYPE_INVENTORY_EMPTY => "You aren't carrying anything",
            self::TYPE_INVENTORY_FULL => "You are overburdened.",
            default => '',
        };
    }
}
