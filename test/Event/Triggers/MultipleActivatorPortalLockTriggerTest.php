<?php

namespace AdventureGame\Test\Event\Triggers;

use AdventureGame\Event\Triggers\ItemComparison;
use AdventureGame\Event\Triggers\MultipleActivatorPortalLockTrigger;
use AdventureGame\Item\Item;
use AdventureGame\Location\Portal;
use AdventureGame\Test\FrameworkTest;

class MultipleActivatorPortalLockTriggerTest extends FrameworkTest
{
    public function testExecute()
    {
        $gameController = $this->createGameController();

        $portal = new Portal(
            'test-portal',
            'Test Portal',
            'A Test Portal',
            ['portal'],
            'in',
            'nowhere'
        );
        $portal->setMutable(true);
        $portal->setLocked(true);

        $switch1 = new Item(
            'switch1',
            'Switch 1',
            "There's no telling what this switch does.",
            ['one']
        );
        $switch1->setActivatable(true);
        $switch1->setDeactivatable(true);
        $switch1->setAcquirable(false);

        $switch2 = new Item(
            'switch2',
            'Switch 2',
            "There's no telling what this switch does.",
            ['two']
        );
        $switch2->setActivatable(true);
        $switch2->setDeactivatable(true);
        $switch2->setAcquirable(false);

        $switch3 = new Item(
            'switch3',
            'Switch 3',
            "There's no telling what this switch does.",
            ['three']
        );
        $switch3->setActivatable(true);
        $switch3->setDeactivatable(true);
        $switch3->setAcquirable(false);

        $activators = [$switch1, $switch2, $switch3];

        $comp1 = new ItemComparison(true);
        $comp2 = new ItemComparison(false);
        $comp3 = new ItemComparison(true);

        $comparisons = [$comp1, $comp2, $comp3];

        $trigger = new MultipleActivatorPortalLockTrigger($activators, $comparisons, $portal);

        // Invalid state.
        $switch1->setActivated(false);
        $switch2->setActivated(false);
        $switch3->setActivated(false);
        $trigger->execute($gameController);

        $this->assertTrue($portal->getMutable());
        $this->assertTrue($portal->getLocked());

        // Valid state.
        $switch1->setActivated(true);
        $switch2->setActivated(false);
        $switch3->setActivated(true);
        $trigger->execute($gameController);

        $this->assertTrue($portal->getMutable());
        $this->assertFalse($portal->getLocked());

        // Invalid state.
        $switch2->setActivated(true);
        $trigger->execute($gameController);

        $this->assertTrue($portal->getMutable());
        $this->assertTrue($portal->getLocked());
    }
}
