<?php

namespace AdventureGame\Event\Triggers;

use AdventureGame\Entity\ActivatableEntityInterface;
use AdventureGame\Event\InfiniteUseTrigger;
use AdventureGame\Game\GameController;
use AdventureGame\Location\Portal;
use AdventureGame\Response\Response;

/**
 * Class TogglePortalLockTrigger toggles the lock state of a portal based on the state of an
 * activator.
 * @package AdventureGame\Event\Triggers
 */
class TogglePortalLockTrigger extends InfiniteUseTrigger
{
    public function __construct(
        protected ActivatableEntityInterface $activator,
        protected Portal $portal
    ) {
    }

    /**
     * Set the portal lock state to inverse of the activator state.
     * @param GameController $gameController
     * @return Response|null
     */
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