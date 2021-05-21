<?php

namespace AdventureGame\Event;

use AdventureGame\Game\Exception\PlayerLocationNotSetException;
use AdventureGame\Game\GameController;
use AdventureGame\Response\Response;

class EventController
{
    private array $events = [];

    public function addEvent(EventInterface $event)
    {
        $this->events[] = $event;
    }

    /**
     * Process take item events for current player location.
     * @param GameController $gameController
     * @param string $itemId
     * @throws PlayerLocationNotSetException
     */
    public function processTakeItemEvents(GameController $gameController, string $itemId): ?Response
    {
        $locationId = $gameController->mapController->getPlayerLocation()->getId();

        foreach ($this->events as $event) {
            if (
                is_a($event, AbstractInventoryEvent::class)
                && $event->matchItemId($itemId)
                && $event->matchLocationId($locationId)
            ) {
                return $event->trigger($gameController);
            }
        }

        return null;
    }
}