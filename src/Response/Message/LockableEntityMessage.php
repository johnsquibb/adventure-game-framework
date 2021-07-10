<?php

namespace AdventureGame\Response\Message;

use AdventureGame\Entity\EntityInterface;
use AdventureGame\Item\ItemInterface;
use AdventureGame\Response\MessageInterface;

/**
 * Class LockableEntityMessage builds messages pertaining to lock/unlock actions.
 * @package AdventureGame\Response\Message
 */
class LockableEntityMessage implements MessageInterface
{
    public const TYPE_LOCK = 'lock';
    public const TYPE_UNLOCK = 'unlock';

    public function __construct(
        private EntityInterface $entity,
        private ItemInterface $key,
        private string $messageType
    ) {
    }

    public function toString(): string
    {
        return match ($this->messageType) {
            self::TYPE_LOCK => sprintf(
                "Locked '%s' with '%s'",
                $this->entity->getName(),
                $this->key->getName()
            ),
            self::TYPE_UNLOCK => sprintf(
                "Unlocked '%s' with '%s'",
                $this->entity->getName(),
                $this->key->getName()
            ),
            default => '',
        };
    }
}
