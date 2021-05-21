<?php

namespace AdventureGame\Event\Triggers;

use AdventureGame\Event\AbstractTrigger;
use AdventureGame\Game\GameController;
use AdventureGame\Item\Item;

class AddItemToInventoryTrigger extends AbstractTrigger
{
    private int $triggerCount = 0;

    public function __construct(private Item $item, private int $numberOfUses = 1)
    {
    }

    public function execute(GameController $gameController): void
    {
        if ($this->triggerCount < $this->numberOfUses) {
            $gameController->playerController->addItemToPlayerInventory($this->item);
            $this->triggerCount++;
        }
    }
}