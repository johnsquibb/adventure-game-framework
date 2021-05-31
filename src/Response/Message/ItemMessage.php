<?php

namespace AdventureGame\Response\Message;

use AdventureGame\Item\ItemInterface;
use AdventureGame\Response\MessageInterface;

/**
 * Class ItemMessage builds messages pertaining to items.
 * @package AdventureGame\Response\Message
 */
class ItemMessage implements MessageInterface
{
    public const TYPE_ACTIVATE = 'activate';
    public const TYPE_ADD = 'add';
    public const TYPE_DEACTIVATE = 'deactivate';
    public const TYPE_REMOVE = 'remove';

    public function __construct(private ItemInterface $item, private string $messageType)
    {
    }

    public function toString(): string
    {
        return match ($this->messageType) {
            self::TYPE_ADD => sprintf("Added '%s' to inventory", $this->item->getName()),
            self::TYPE_REMOVE => sprintf("Removed '%s' from inventory", $this->item->getName()),
            self::TYPE_ACTIVATE => sprintf("Activated '%s'", $this->item->getName()),
            self::TYPE_DEACTIVATE => sprintf("Deactivated '%s'", $this->item->getName()),
            default => '',
        };
    }
}