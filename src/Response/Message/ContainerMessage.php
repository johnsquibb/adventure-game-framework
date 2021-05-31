<?php

namespace AdventureGame\Response\Message;

use AdventureGame\Response\MessageInterface;

/**
 * Class ContainerMessage builds messages pertaining to containers.
 * @package AdventureGame\Response\Message
 */
class ContainerMessage implements MessageInterface
{
    public const TYPE_CONTAINER_EMPTY = 'container-empty';
    public const TYPE_CONTAINER_FULL = 'container-full';

    public function __construct(private string $name, private string $messageType)
    {
    }

    public function toString(): string
    {
        return match ($this->messageType) {
            self::TYPE_CONTAINER_EMPTY => sprintf("There's nothing inside %s", $this->name),
            self::TYPE_CONTAINER_FULL => sprintf("%s is full", $this->name),
            default => '',
        };
    }
}