<?php

namespace AdventureGame\Event;

use AdventureGame\Game\GameController;
use AdventureGame\Response\Response;

abstract class AbstractEvent implements EventInterface
{
    public const MATCH_ALL = '*';

    protected TriggerInterface $trigger;
    protected string $matchItemId = '';
    protected string $matchLocationId = '';

    /**
     * Execute the event trigger.
     * @param GameController $gameController
     * @return Response|null
     */
    public function trigger(GameController $gameController): ?Response
    {
        return $this->trigger->execute($gameController);
    }

    /**
     * Match Item.
     * @param string $itemId
     * @return bool
     */
    public function matchItemId(string $itemId): bool
    {
        return $this->matchItemId === self::MATCH_ALL || $this->matchItemId === $itemId;
    }

    /**
     * Match Location
     * @param string $locationId
     * @return bool
     */
    public function matchLocationId(string $locationId): bool
    {
        return $this->matchLocationId === self::MATCH_ALL || $this->matchLocationId === $locationId;
    }
}