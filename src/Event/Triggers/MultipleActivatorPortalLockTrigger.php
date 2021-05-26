<?php

namespace AdventureGame\Event\Triggers;

use AdventureGame\Entity\ActivatableEntityInterface;
use AdventureGame\Event\InfiniteUseTrigger;
use AdventureGame\Game\GameController;
use AdventureGame\Location\Portal;
use AdventureGame\Response\Response;
use Exception;

class MultipleActivatorPortalLockTrigger extends InfiniteUseTrigger
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

    public function execute(GameController $gameController): ?Response
    {
        $locked = false;

        foreach ($this->activators as $index => $activator) {
            if ($activator instanceof ActivatableEntityInterface) {
                $comparison = $this->comparisons[$index];
                if ($comparison instanceof ItemComparison) {
                    if ($activator->getActivated() !== $comparison->getActivated()) {
                        $locked = true;
                        break;
                    }
                }
            }
        }

        $this->portal->setLocked($locked);

        return null;
    }
}