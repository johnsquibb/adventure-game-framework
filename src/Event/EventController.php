<?php

namespace AdventureGame\Event;

use AdventureGame\Event\Events\DropItemEvent;
use AdventureGame\Event\Events\EnterLocationEvent;
use AdventureGame\Event\Events\ExitLocationEvent;
use AdventureGame\Event\Events\TakeItemEvent;
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
     * @return Response|null
     * @throws PlayerLocationNotSetException
     */
    public function processTakeItemEvents(GameController $gameController, string $itemId): ?Response
    {
        $locationId = $gameController->mapController->getPlayerLocation()->getId();

        foreach ($this->events as $event) {
            if (
                $event instanceof TakeItemEvent
                && $event->matchItemId($itemId)
                && $event->matchLocationId($locationId)
            ) {
                return $event->trigger($gameController);
            }
        }

        return null;
    }

    /**
     * Process drop item events for current player location.
     * @param GameController $gameController
     * @param string $itemId
     * @return Response|null
     * @throws PlayerLocationNotSetException
     */
    public function processDropItemEvents(GameController $gameController, string $itemId): ?Response
    {
        $locationId = $gameController->mapController->getPlayerLocation()->getId();

        foreach ($this->events as $event) {
            if (
                $event instanceof DropItemEvent
                && $event->matchItemId($itemId)
                && $event->matchLocationId($locationId)
            ) {
                return $event->trigger($gameController);
            }
        }

        return null;
    }

    /**
     * Process enter location events.
     * @param GameController $gameController
     * @param string $locationId
     * @return Response|null
     */
    public function processEnterLocationEvents(
        GameController $gameController,
        string $locationId
    ): ?Response {
        foreach ($this->events as $event) {
            if (
                $event instanceof EnterLocationEvent
                && $event->matchLocationId($locationId)
            ) {
                return $event->trigger($gameController);
            }
        }

        return null;
    }

    /**
     * Process exit location events
     * @param GameController $gameController
     * @param string $locationId
     * @return Response|null
     */
    public function processExitLocationEvents(
        GameController $gameController,
        string $locationId
    ): ?Response {
        foreach ($this->events as $event) {
            if (
                $event instanceof ExitLocationEvent
                && $event->matchLocationId($locationId)
            ) {
                return $event->trigger($gameController);
            }
        }

        return null;
    }
}