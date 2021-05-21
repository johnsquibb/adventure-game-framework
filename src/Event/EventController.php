<?php

namespace AdventureGame\Event;

use AdventureGame\Game\GameController;

class EventController
{
    private array $events = [];

    public function addEvent(EventInterface $event)
    {
        $this->events[] = $event;
    }

    public function processInventoryTakeEvents(GameController $gameController, string $itemId): void
    {
        $locationId = $gameController->mapController->getPlayerLocation()->getId();

        foreach ($this->events as $event) {
            if (
                is_a($event, AbstractInventoryEvent::class)
                && $event->matchItemId($itemId)
                && $event->matchLocationId($locationId)
            ) {
                $event->trigger($gameController);
            }
        }
    }
}