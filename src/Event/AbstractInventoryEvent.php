<?php

namespace AdventureGame\Event;

abstract class AbstractInventoryEvent implements EventInterface
{
    public const MATCH_ALL = '*';

    public function __construct(
        protected TriggerInterface $trigger,
        private string $matchItemId = '',
        private string $matchLocationId = '',
    ) {
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