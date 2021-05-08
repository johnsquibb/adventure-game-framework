<?php

namespace AdventureGame\Game;

use AdventureGame\Game\Exception\InvalidExitException;
use AdventureGame\Game\Exception\PlayerLocationNotSetException;
use AdventureGame\Item\ItemInterface;
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
     * @return ItemInterface|null
     * @throws PlayerLocationNotSetException
     */
    public function takeItemById(string $itemId): ?ItemInterface
    {
        $location = $this->getPlayerLocation();

        $item = $location->items->getItemById($itemId);
        if ($item !== null) {
            $location->items->removeItemById($item->id);
        }

        return $item;
    }

    /**
     * Get all items that match tag.
     * @param string $tag
     * @return array
     * @throws PlayerLocationNotSetException
     */
    public function takeItemsByTag(string $tag): array
    {
        $location = $this->getPlayerLocation();

        $items = $location->items->getItemsByTag($tag);

        foreach ($items as $item) {
            $location->items->removeItemById($item->id);
        }

        return $items;
    }

    /**
     * Drop an item to current player location.
     * @param ItemInterface $item
     * @throws PlayerLocationNotSetException
     */
    public function dropItem(ItemInterface $item): void
    {
        $location = $this->getPlayerLocation();
        $location->items->addItem($item);
    }
}