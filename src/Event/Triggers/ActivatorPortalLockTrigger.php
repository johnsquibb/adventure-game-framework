<?php

namespace AdventureGame\Event\Triggers;

use AdventureGame\Entity\ActivatableEntityInterface;
use AdventureGame\Event\InfiniteUseTrigger;
use AdventureGame\Event\Triggers\Comparisons\ActivatedComparison;
use AdventureGame\Game\GameController;
use AdventureGame\Location\Portal;
use AdventureGame\Response\Response;
use Exception;

/**
 * Class ActivatorPortalLockTrigger locks or unlocks a portal based on the state on the
 * state of activators vs. an equal number of comparisons.
 * @package AdventureGame\Event\Triggers
 */
class ActivatorPortalLockTrigger extends InfiniteUseTrigger
{
    /**
     * @throws Exception
     */
    public function __construct(
        protected array $activators,
        protected array $comparisons,
        protected Portal $portal
    ) {
        if (count($this->activators) !== count($this->comparisons)) {
            throw new Exception('Expected equal number of activators and comparisons');
        }
    }

    /**
     * Check states of activators against the comparisons and unlock or lock the portal.
     * @param GameController $gameController
     * @return Response|null
     */
    public function execute(GameController $gameController): ?Response
    {
        $response = new Response();

        foreach ($this->activators as $index => $activator) {
            if ($activator instanceof ActivatableEntityInterface) {
                $comparison = $this->comparisons[$index];
                if ($comparison instanceof ActivatedComparison) {
                    if ($activator->getActivated() !== $comparison->getActivated()) {
                        if (!$this->portal->getLocked()) {
                            $response->addMessage($this->portal->getName() . ' locked');
                        }
                        $this->portal->setLocked(true);
                        return $response;
                    }
                }
            }
        }

        $this->portal->setLocked(false);
        $response->addMessage($this->portal->getName() . ' unlocked');

        return $response;
    }
}