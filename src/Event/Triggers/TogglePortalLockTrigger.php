<?php

namespace AdventureGame\Event\Triggers;

use AdventureGame\Entity\ActivatableEntityInterface;
use AdventureGame\Event\InfiniteUseTrigger;
use AdventureGame\Game\GameController;
use AdventureGame\Location\Portal;
use AdventureGame\Response\Response;

class TogglePortalLockTrigger extends InfiniteUseTrigger
{
    public function __construct(
        protected ActivatableEntityInterface $activator,
        protected Portal $portal
    ) {
    }

    public function execute(GameController $gameController): ?Response
    {
        // Unlock
        if ($this->activator->getActivated()) {
            $this->triggerCount++;
            if ($this->portal->getMutable()) {
                $this->portal->setLocked(false);
            }
        }

        // Lock
        if (!$this->activator->getActivated()) {
            $this->triggerCount++;
            if ($this->portal->getMutable()) {
                $this->portal->setLocked(true);
            }
        }

        return null;
    }
}