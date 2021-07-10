<?php

namespace AdventureGame\Response\Message;

use AdventureGame\Response\MessageInterface;

/**
 * Class UnableMessage builds messages pertaining to errors, invalid commands.
 * @package AdventureGame\Response\Message
 */
class UnableMessage implements MessageInterface
{
    public const TYPE_ALREADY_ACTIVATED = 'already-activated';
    public const TYPE_ALREADY_DEACTIVATED = 'already-deactivated';
    public const TYPE_CANNOT_ACTIVATE = 'cannot-activate';
    public const TYPE_CANNOT_DEACTIVATE = 'cannot-deactivate';
    public const TYPE_CANNOT_LOCK = 'cannot-lock';
    public const TYPE_CANNOT_READ = 'cannot-read';
    public const TYPE_CANNOT_TAKE = 'cannot-take';
    public const TYPE_CANNOT_UNLOCK = 'cannot-unlock';
    public const TYPE_CONTAINER_NOT_FOUND = 'container-not-found';
    public const TYPE_CONTAINER_NOT_REVEALED = 'container-not-revealed';
    public const TYPE_CONTAINER_NOT_LOCKABLE = 'container-not-lockable';
    public const TYPE_CONTAINER_NOT_UNLOCKABLE = 'container-not-unlockable';
    public const TYPE_ITEM_CANNOT_PUT_THERE = 'item-cannot-put-there';
    public const TYPE_ITEM_NOT_ACCESSIBLE = 'item-not-accessible';
    public const TYPE_ITEM_NOT_DISCOVERED = 'item-not-discovered';
    public const TYPE_ITEM_NOT_FOUND = 'item-not-found';
    public const TYPE_ITEM_NOT_IN_INVENTORY = 'item-not-in-inventory';
    public const TYPE_MISSING_KEY = 'missing-key';
    public const TYPE_NOTHING_TO_LOCK = 'nothing-to-lock';
    public const TYPE_NOTHING_TO_UNLOCK = 'nothing-to-unlock';
    public const TYPE_PORTAL_LOCKED = 'portal-locked';
    public const TYPE_PORTAL_NOT_LOCKABLE = 'portal-not-lockable';
    public const TYPE_PORTAL_NOT_UNLOCKABLE = 'portal-not-unlockable';

    public function __construct(private string $name, private string $messageType)
    {
    }

    public function toString(): string
    {
        return match ($this->messageType) {
            self::TYPE_ITEM_NOT_FOUND => sprintf("You don't see '%s'", $this->name),
            self::TYPE_ITEM_NOT_DISCOVERED => sprintf("You haven't found '%s'", $this->name),
            self::TYPE_ITEM_NOT_ACCESSIBLE => sprintf("You can't access '%s'", $this->name),
            self::TYPE_CONTAINER_NOT_FOUND => sprintf("You don't see '%s'", $this->name),
            self::TYPE_CONTAINER_NOT_REVEALED => sprintf("You haven't opened '%s' yet", $this->name),
            self::TYPE_NOTHING_TO_UNLOCK => sprintf("There is no '%s' to unlock", $this->name),
            self::TYPE_NOTHING_TO_LOCK => sprintf("There is no '%s' to lock", $this->name),
            self::TYPE_CANNOT_UNLOCK => sprintf("Can't unlock '%s'", $this->name),
            self::TYPE_CANNOT_LOCK => sprintf("Can't lock '%s'", $this->name),
            self::TYPE_MISSING_KEY => sprintf("You need the key to '%s'", $this->name),
            self::TYPE_CANNOT_READ => sprintf("You can't read '%s'", $this->name),
            self::TYPE_CANNOT_TAKE => sprintf("You can't take '%s'", $this->name),
            self::TYPE_CANNOT_ACTIVATE => sprintf("You can't activate '%s'", $this->name),
            self::TYPE_CANNOT_DEACTIVATE => sprintf("You can't deactivate '%s'", $this->name),
            self::TYPE_ALREADY_ACTIVATED => sprintf("'%s' is already activated", $this->name),
            self::TYPE_ALREADY_DEACTIVATED => sprintf("'%s' is already deactivated", $this->name),
            self::TYPE_PORTAL_LOCKED => sprintf("'%s' is locked", $this->name),
            self::TYPE_PORTAL_NOT_LOCKABLE => sprintf("'%s' can't be locked", $this->name),
            self::TYPE_PORTAL_NOT_UNLOCKABLE => sprintf("'%s' can't be unlocked", $this->name),
            self::TYPE_ITEM_NOT_IN_INVENTORY => sprintf("You don't have '%s'", $this->name),
            self::TYPE_ITEM_CANNOT_PUT_THERE => sprintf("You can't put '%s' there", $this->name),
            default => '',
        };
    }
}
