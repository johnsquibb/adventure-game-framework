<?php

namespace AdventureGame\Event\Triggers;

use AdventureGame\Event\FiniteUseTrigger;
use AdventureGame\Game\GameController;
use AdventureGame\Location\Location;
use AdventureGame\Location\Portal;
use AdventureGame\Response\Response;

class AddLocationToMapUseTrigger extends FiniteUseTrigger
{
    protected array $exits = [];

    public function __construct(protected Location $location, int $numberOfUses = 1)
    {
        $this->numberOfUses = $numberOfUses;
    }

    /**
     * Add an exit to location.
     * @param string $locationId
     * @param Portal $portal
     */
    public function addExit(string $locationId, Portal $portal): void
    {
        $this->exits[$locationId] = $portal;
    }

    /**
     * Add location to map.
     * @param GameController $gameController
     * @return Response|null
     */
    public function execute(GameController $gameController): ?Response
    {
        if ($this->triggerCount < $this->numberOfUses) {
            $gameController->mapController->addLocation($this->location);
            $this->triggerCount++;
        }

        foreach ($this->exits as $fromLocationId => $entrance) {
            $fromLocation = $gameController->mapController->getLocationById($fromLocationId);
            if ($fromLocation instanceof Location) {
                $fromLocation->addExit($entrance);
            }
        }

        return null;
    }
}