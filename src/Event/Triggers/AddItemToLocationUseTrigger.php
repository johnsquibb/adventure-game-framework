<?php

namespace AdventureGame\Event\Triggers;

use AdventureGame\Event\FiniteUseTrigger;
use AdventureGame\Game\Exception\PlayerLocationNotSetException;
use AdventureGame\Game\GameController;
use AdventureGame\Item\ItemInterface;
use AdventureGame\Response\Response;

class AddItemToLocationUseTrigger extends FiniteUseTrigger
{
    public function __construct(protected ItemInterface $item, int $numberOfUses = 1)
    {
        $this->numberOfUses = $numberOfUses;
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
