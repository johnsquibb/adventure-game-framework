<?php

namespace AdventureGame\Event\Triggers;

use AdventureGame\Event\AbstractTrigger;
use AdventureGame\Game\GameController;
use AdventureGame\Item\Item;

class AddItemToInventoryTrigger extends AbstractTrigger
{
    public function __construct(private Item $item)
    {
    }

    public function execute(GameController $gameController): void
    {
        $gameController->playerController->addItemToPlayerInventory($this->item);
    }
}