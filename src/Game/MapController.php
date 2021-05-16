<?php

namespace AdventureGame\Game;

use AdventureGame\Game\Exception\ExitIsLockedException;
use AdventureGame\Game\Exception\InvalidExitException;
use AdventureGame\Game\Exception\PlayerLocationNotSetException;
use AdventureGame\Item\ItemInterface;
use AdventureGame\Location\Location;

/**
 * Class MapController provides methods for interacting with locations on the game map.
 * @package AdventureGame\Game
 */
class MapController
{
    private ?Location $playerLocation;

    public function __construct(private array $locations)
    {
    }

    /**
     * Drop an item to current player location.
     * @param ItemInterface $item
     * @throws PlayerLocationNotSetException
     */
    public function dropItem(ItemInterface $item): void
    {
        $location = $this->getPlayerLocation();
        $location->getContainer()->addItem($item);
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
     * Get count of items in current player location.
     * @return int
     * @throws PlayerLocationNotSetException
     */
    public function getItemCount(): int
    {
        return $this->getPlayerLocation()->getContainer()->countItems();
    }

    /**
     * @param string $direction The direction in which to move the player.
     * @throws InvalidExitException|PlayerLocationNotSetException|ExitIsLockedException
     */
    public function movePlayer(string $direction): void
    {
        $location = $this->getPlayerLocation();

        $portal = $location->getExitInDirection($direction);
        if ($portal === null) {
            throw new InvalidExitException('Invalid exit');
        }

        if ($portal->getLocked() === true) {
            throw new ExitIsLockedException();
        }

        $this->setPlayerLocationById($portal->destinationLocationId);
    }

    /**
     * Set the current player location by id.
     * @param string $locationId
     */
    public function setPlayerLocationById(string $locationId)
    {
        foreach ($this->locations as $location) {
            if ($location instanceof Location && $location->getId() === $locationId) {
                $this->playerLocation = $location;
            }
        }
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

        $item = $location->getContainer()->getItemById($itemId);
        if ($item !== null) {
            $location->getContainer()->removeItemById($item->getId());
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

        $items = $location->getContainer()->getItemsByTag($tag);

        foreach ($items as $item) {
            $location->getContainer()->removeItemById($item->getId());
        }

        return $items;
    }
}