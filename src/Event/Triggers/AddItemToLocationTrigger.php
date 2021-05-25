<?php

namespace AdventureGame\Event\Triggers;

use AdventureGame\Event\AbstractTrigger;
use AdventureGame\Game\Exception\PlayerLocationNotSetException;
use AdventureGame\Game\GameController;
use AdventureGame\Item\Item;
use AdventureGame\Response\Response;

class AddItemToLocationTrigger extends AbstractTrigger
{
    public function __construct(protected Item $item, protected int $numberOfUses = 1)
    {
    }

    /**
     * Add item to player location.
     * @param GameController $gameController
     * @return Response|null
     * @throws PlayerLocationNotSetException
     */
    public function execute(GameController $gameController): ?Response
    {
        if ($this->triggerCount < $this->numberOfUses) {
            $gameController->mapController->addItem($this->item);
            $this->triggerCount++;

            $response = new Response();
            $response->addMessage("Revealed \"{$this->item->getName()}\".");
            return $response;
        }

        return null;
    }
}