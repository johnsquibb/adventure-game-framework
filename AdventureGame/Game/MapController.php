<?php

namespace AdventureGame\Game;

use AdventureGame\Game\Exception\InvalidExitException;
use AdventureGame\Game\Exception\PlayerLocationNotSetException;
use AdventureGame\Location\Location;

class MapController
{
    private ?Location $playerLocation;

    public function __construct(private array $locations)
    {
    }

    /**
     * Set the current player location by id.
     * @param string $locationId
     */
    public function setPlayerLocationById(string $locationId)
    {
        foreach ($this->locations as $location) {
            if ($location instanceof Location && $location->id === $locationId) {
                $this->playerLocation = $location;
            }
        }
    }

    /**
     * The current location of the player, if set.
     * @return Location|null
     */
    public function getPlayerLocation(): ?Location
    {
        return $this->playerLocation ?? null;
    }

    /**
     * @param string $direction The direction in which to move the player.
     * @throws InvalidExitException|PlayerLocationNotSetException
     */
    public function movePlayer(string $direction): void
    {
        if ($this->getPlayerLocation() === null) {
            throw new PlayerLocationNotSetException('Player location not set');
        }

        $portal = $this->playerLocation->getExitInDirection($direction);
        if ($portal === null) {
            throw new InvalidExitException('Invalid exit');
        }

        $this->setPlayerLocationById($portal->destinationLocationId);
    }
}