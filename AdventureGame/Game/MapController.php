<?php

namespace AdventureGame\Game;

use AdventureGame\Game\Exception\InvalidExitException;
use AdventureGame\Game\Exception\PlayerLocationNotSetException;
use AdventureGame\Item\Item;
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
     * @return Location
     * @throws PlayerLocationNotSetException
     */
    public function getPlayerLocation(): Location
    {
        if (!isset($this->playerLocation)) {
            throw new PlayerLocationNotSetException('Player location not set');
        }

        return $this->playerLocation;
    }

    /**
     * @param string $direction The direction in which to move the player.
     * @throws InvalidExitException|PlayerLocationNotSetException
     */
    public function movePlayer(string $direction): void
    {
        $location = $this->getPlayerLocation();

        $portal = $location->getExitInDirection($direction);
        if ($portal === null) {
            throw new InvalidExitException('Invalid exit');
        }

        $this->setPlayerLocationById($portal->destinationLocationId);
    }

    /**
     * Take an item by id from current player location.
     * @param string $itemId
     * @return Item|null
     * @throws PlayerLocationNotSetException
     */
    public function takeItemById(string $itemId): ?Item
    {
        $location = $this->getPlayerLocation();

        $item = $location->items->getItemById($itemId);
        if ($item !== null) {
            $location->items->removeItemById($item->id);
        }

        return $item;
    }

    /**
     * Drop an item to current player location.
     * @param Item $item
     * @throws PlayerLocationNotSetException
     */
    public function dropItem(Item $item): void
    {
        $location = $this->getPlayerLocation();
        $location->items->addItem($item);
    }
}