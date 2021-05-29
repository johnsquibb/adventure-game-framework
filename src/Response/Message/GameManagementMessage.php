<?php

namespace AdventureGame\Response\Message;

use AdventureGame\Response\MessageInterface;

class GameManagementMessage implements MessageInterface
{
    public const TYPE_NEW_GAME_STARTED = 'new-game-started';
    public const TYPE_GAME_SAVED = 'game-saved';
    public const TYPE_GAME_LOADED = 'game-loaded';
    public const TYPE_GAME_NOT_LOADED = 'game-not-loaded';
    public const TYPE_CANNOT_FIND_SAVE_FILE = 'cannot-find-save-file';
    public const TYPE_UNSERIALIZE_ERROR = 'unserialize-error';

    public function __construct(private string $messageType)
    {
    }

    public function toString(): string
    {
        return match ($this->messageType) {
            self::TYPE_NEW_GAME_STARTED => "New game started",
            self::TYPE_GAME_SAVED => "Game saved",
            self::TYPE_GAME_LOADED => "Game loaded",
            self::TYPE_GAME_NOT_LOADED => "Could not load saved game",
            self::TYPE_CANNOT_FIND_SAVE_FILE => "Could not find save file.",
            self::TYPE_UNSERIALIZE_ERROR => "Save file could not be recovered, it may be corrupted.",
            default => '',
        };
    }
}