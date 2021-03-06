<?php

namespace AdventureGame\Event;

use AdventureGame\Event\Events\ActivateItemEvent;
use AdventureGame\Event\Events\DeactivateItemEvent;
use AdventureGame\Event\Events\DropItemEvent;
use AdventureGame\Event\Events\EnterLocationEvent;
use AdventureGame\Event\Events\ExitLocationEvent;
use AdventureGame\Event\Events\HasActivatedItemEvent;
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
     * Process activate item events.
     * @param GameController $gameController
     * @param string $itemId
     * @return Response
     * @throws PlayerLocationNotSetException
     */
    public function processActivateItemEvents(
        GameController $gameController,
        string $itemId
    ): Response {
        $locationId = $gameController->mapController->getPlayerLocation()->getId();

        $response = new Response();

        foreach ($this->events as $event) {
            if (
                $event instanceof ActivateItemEvent
                && $event->matchItemId($itemId)
                && $event->matchLocationId($locationId)
            ) {
                $triggerResponse = $event->trigger($gameController);
                if ($triggerResponse) {
                    $response->addMessages($triggerResponse->getMessages());
                }
            }
        }

        return $response;
    }

    /**
     * Process deactivate item events.
     * @param GameController $gameController
     * @param string $itemId
     * @return Response
     * @throws PlayerLocationNotSetException
     */
    public function processDeactivateItemEvents(
        GameController $gameController,
        string $itemId
    ): Response {
        $locationId = $gameController->mapController->getPlayerLocation()->getId();

        $response = new Response();

        foreach ($this->events as $event) {
            if (
                $event instanceof DeactivateItemEvent
                && $event->matchItemId($itemId)
                && $event->matchLocationId($locationId)
            ) {
                $triggerResponse = $event->trigger($gameController);
                if ($triggerResponse) {
                    $response->addMessages($triggerResponse->getMessages());
                }
            }
        }

        return $response;
    }

    /**
     * Process drop item events for current player location.
     * @param GameController $gameController
     * @param string $itemId
     * @return Response
     * @throws PlayerLocationNotSetException
     */
    public function processDropItemEvents(GameController $gameController, string $itemId): Response
    {
        $locationId = $gameController->mapController->getPlayerLocation()->getId();

        $response = new Response();

        foreach ($this->events as $event) {
            if (
                $event instanceof DropItemEvent
                && $event->matchItemId($itemId)
                && $event->matchLocationId($locationId)
            ) {
                $triggerResponse = $event->trigger($gameController);
                if ($triggerResponse) {
                    $response->addMessages($triggerResponse->getMessages());
                }
            }
        }

        return $response;
    }

    /**
     * Process enter location events.
     * @param GameController $gameController
     * @param string $locationId
     * @return Response
     */
    public function processEnterLocationEvents(
        GameController $gameController,
        string $locationId
    ): Response {
        $response = new Response();

        foreach ($this->events as $event) {
            if (
                $event instanceof EnterLocationEvent
                && $event->matchLocationId($locationId)
            ) {
                $triggerResponse = $event->trigger($gameController);
                if ($triggerResponse) {
                    $response->addMessages($triggerResponse->getMessages());
                }
            }
        }

        return $response;
    }

    /**
     * Process exit location events
     * @param GameController $gameController
     * @param string $locationId
     * @return Response
     */
    public function processExitLocationEvents(
        GameController $gameController,
        string $locationId
    ): Response {
        $response = new Response();

        foreach ($this->events as $event) {
            if (
                $event instanceof ExitLocationEvent
                && $event->matchLocationId($locationId)
            ) {
                $triggerResponse = $event->trigger($gameController);
                if ($triggerResponse) {
                    $response->addMessages($triggerResponse->getMessages());
                }
            }
        }

        return $response;
    }

    /**
     * Process has activated item events.
     * @param GameController $gameController
     * @param string $itemId
     * @return Response
     * @throws PlayerLocationNotSetException
     */
    public function processHasActivatedItemEvents(
        GameController $gameController,
        string $itemId
    ): Response {
        $locationId = $gameController->mapController->getPlayerLocation()->getId();

        $response = new Response();

        foreach ($this->events as $event) {
            if (
                $event instanceof HasActivatedItemEvent
                && $event->matchItemId($itemId)
                && $event->matchLocationId($locationId)
            ) {
                $triggerResponse = $event->trigger($gameController);
                if ($triggerResponse) {
                    $response->addMessages($triggerResponse->getMessages());
                }
            }
        }

        return $response;
    }

    /**
     * Process take item events for current player location.
     * @param GameController $gameController
     * @param string $itemId
     * @return Response
     * @throws PlayerLocationNotSetException
     */
    public function processTakeItemEvents(GameController $gameController, string $itemId): Response
    {
        $locationId = $gameController->mapController->getPlayerLocation()->getId();

        $response = new Response();

        foreach ($this->events as $event) {
            if (
                $event instanceof TakeItemEvent
                && $event->matchItemId($itemId)
                && $event->matchLocationId($locationId)
            ) {
                $triggerResponse = $event->trigger($gameController);
                if ($triggerResponse) {
                    $response->addMessages($triggerResponse->getMessages());
                }
            }
        }

        return $response;
    }
}
