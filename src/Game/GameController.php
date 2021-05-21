<?php

namespace AdventureGame\Game;

use AdventureGame\Event\EventController;
use AdventureGame\Game\Exception\InvalidSaveDirectoryException;

/**
 * Class GameController provides methods for accessing common game components.
 * @package AdventureGame\Game
 */
class GameController
{
    private string $saveDirectory = '';

    public function __construct(
        public MapController $mapController,
        public PlayerController $playerController,
        public EventController $eventController,
    ) {
    }

    /**
     * Rehydrate this object from a save state object.
     * @param GameController $gameController
     */
    public function hydrateFromSave(GameController $gameController) {
        $this->mapController = $gameController->mapController;
        $this->playerController = $gameController->playerController;
        $this->eventController = $gameController->eventController;
    }

    /**
     * Set the save directory path.
     * @param string $path
     * @throws InvalidSaveDirectoryException
     */
    public function setSaveDirectory(string $path): void
    {
        if (!is_dir($path)) {
            throw new InvalidSaveDirectoryException('save directory does not exist: ' . $path);
        }

        $this->saveDirectory = $path;
    }

    /**
     * Get the save directory path.
     * @return string
     * @throws InvalidSaveDirectoryException
     */
    public function getSaveDirectory(): string
    {
        if (empty($this->saveDirectory)) {
            throw new InvalidSaveDirectoryException('save directory not set');
        }

        return $this->saveDirectory;
    }
}