<?php

namespace AdventureGame\Test\Builder;

use AdventureGame\Builder\SceneBuilder;
use AdventureGame\Item\ContainerItem;
use AdventureGame\Item\ItemInterface;
use AdventureGame\Location\Location;
use AdventureGame\Location\Portal;
use AdventureGameMarkupLanguage\Lexer;
use AdventureGameMarkupLanguage\Parser;
use AdventureGameMarkupLanguage\Transpiler;
use PHPUnit\Framework\TestCase;

class SceneBuilderTest extends TestCase
{
    public function testTranspileMarkupGetItems()
    {
        $fixture = <<<END
        [ITEM]
        id=one
        [ITEM]
        id=two
        [ITEM]
        id=three
        END;

        $builder = new SceneBuilder(new Transpiler(new Lexer(), new Parser()));
        $builder->transpileMarkup($fixture);
        $items = $builder->getItems();
        $this->assertCount(3, $items);
        $this->assertEquals(['one', 'two', 'three'], array_keys($items));
        $this->assertInstanceOf(ItemInterface::class, $items['one']);
        $this->assertInstanceOf(ItemInterface::class, $items['two']);
        $this->assertInstanceOf(ItemInterface::class, $items['three']);
    }

    public function testTranspileMarkupGetLocations()
    {
        $fixture = <<<END
        [LOCATION]
        id=one
        [LOCATION]
        id=two
        [LOCATION]
        id=three
        END;

        $builder = new SceneBuilder(new Transpiler(new Lexer(), new Parser()));
        $builder->transpileMarkup($fixture);

        $locations = $builder->getLocations();
        $this->assertCount(3, $locations);
        $this->assertEquals(['one', 'two', 'three'], array_keys($locations));
        $this->assertInstanceOf(Location::class, $locations['one']);
        $this->assertInstanceOf(Location::class, $locations['two']);
        $this->assertInstanceOf(Location::class, $locations['three']);
    }

    public function testTranspileMarkupGetLocationsWithItemsAndCapacity()
    {
        $fixture = <<<END
        [ITEM]
        id=itemOne
        [ITEM]
        id=itemTwo
        [ITEM]
        id=itemThree
        [ITEM]
        id=itemFour
        [LOCATION]
        id=one
        items=itemOne,itemTwo
        capacity=1
        [LOCATION]
        id=two
        items=itemTwo,itemOne,itemThree
        capacity=2
        [LOCATION]
        id=three
        items=itemFour,itemThree
        capacity=3
        END;

        $builder = new SceneBuilder(new Transpiler(new Lexer(), new Parser()));
        $builder->transpileMarkup($fixture);

        $locations = $builder->getLocations();
        $this->assertCount(3, $locations);
        $this->assertEquals(['one', 'two', 'three'], array_keys($locations));
        $this->assertInstanceOf(Location::class, $locations['one']);
        $this->assertInstanceOf(Location::class, $locations['two']);
        $this->assertInstanceOf(Location::class, $locations['three']);

        $this->assertEquals(1, $locations['one']->getContainer()->getCapacity());
        $this->assertEquals(2, $locations['two']->getContainer()->getCapacity());
        $this->assertEquals(3, $locations['three']->getContainer()->getCapacity());

        $this->assertCount(2, $locations['one']->getContainer()->getItems());
        $this->assertCount(3, $locations['two']->getContainer()->getItems());
        $this->assertCount(2, $locations['three']->getContainer()->getItems());

        // Location one's items
        $this->assertEquals(
            'itemOne',
            $locations['one']->getContainer()->getItems()[0]->getId()
        );
        $this->assertEquals(
            'itemTwo',
            $locations['one']->getContainer()->getItems()[1]->getId()
        );

        // Location two's items
        $this->assertEquals(
            'itemTwo',
            $locations['two']->getContainer()->getItems()[0]->getId()
        );
        $this->assertEquals(
            'itemOne',
            $locations['two']->getContainer()->getItems()[1]->getId()
        );
        $this->assertEquals(
            'itemThree',
            $locations['two']->getContainer()->getItems()[2]->getId()
        );

        // Location three's items
        $this->assertEquals(
            'itemFour',
            $locations['three']->getContainer()->getItems()[0]->getId()
        );
        $this->assertEquals(
            'itemThree',
            $locations['three']->getContainer()->getItems()[1]->getId()
        );
    }

    public function testTranspileMarkupGetContainers()
    {
        $fixture = <<<END
        [CONTAINER]
        id=one
        [CONTAINER]
        id=two
        [CONTAINER]
        id=three
        END;

        $builder = new SceneBuilder(new Transpiler(new Lexer(), new Parser()));
        $builder->transpileMarkup($fixture);

        $locations = $builder->getContainers();
        $this->assertCount(3, $locations);
        $this->assertEquals(['one', 'two', 'three'], array_keys($locations));
        $this->assertInstanceOf(ContainerItem::class, $locations['one']);
        $this->assertInstanceOf(ContainerItem::class, $locations['two']);
        $this->assertInstanceOf(ContainerItem::class, $locations['three']);
    }

    public function testTranspileMarkupGetContainersWithItemsAndCapacity()
    {
        $fixture = <<<END
        [ITEM]
        id=itemOne
        [ITEM]
        id=itemTwo
        [ITEM]
        id=itemThree
        [ITEM]
        id=itemFour
        [CONTAINER]
        id=one
        items=itemOne,itemTwo
        capacity=1
        [CONTAINER]
        id=two
        items=itemTwo,itemOne,itemThree
        capacity=2
        [CONTAINER]
        id=three
        items=itemFour,itemThree
        capacity=3
        END;

        $builder = new SceneBuilder(new Transpiler(new Lexer(), new Parser()));
        $builder->transpileMarkup($fixture);

        $containers = $builder->getContainers();
        $this->assertCount(3, $containers);
        $this->assertEquals(['one', 'two', 'three'], array_keys($containers));
        $this->assertInstanceOf(ContainerItem::class, $containers['one']);
        $this->assertInstanceOf(ContainerItem::class, $containers['two']);
        $this->assertInstanceOf(ContainerItem::class, $containers['three']);

        $this->assertEquals(1, $containers['one']->getCapacity());
        $this->assertEquals(2, $containers['two']->getCapacity());
        $this->assertEquals(3, $containers['three']->getCapacity());

        $this->assertCount(2, $containers['one']->getItems());
        $this->assertCount(3, $containers['two']->getItems());
        $this->assertCount(2, $containers['three']->getItems());

        // Container one's items
        $this->assertEquals(
            'itemOne',
            $containers['one']->getItems()[0]->getId()
        );
        $this->assertEquals(
            'itemTwo',
            $containers['one']->getItems()[1]->getId()
        );

        // Container two's items
        $this->assertEquals(
            'itemTwo',
            $containers['two']->getItems()[0]->getId()
        );
        $this->assertEquals(
            'itemOne',
            $containers['two']->getItems()[1]->getId()
        );
        $this->assertEquals(
            'itemThree',
            $containers['two']->getItems()[2]->getId()
        );

        // Container three's items
        $this->assertEquals(
            'itemFour',
            $containers['three']->getItems()[0]->getId()
        );
        $this->assertEquals(
            'itemThree',
            $containers['three']->getItems()[1]->getId()
        );
    }

    public function testTranspileMarkupGetPortals()
    {
        $fixture = <<<END
        [PORTAL]
        id=one
        [PORTAL]
        id=two
        [PORTAL]
        id=three
        END;

        $builder = new SceneBuilder(new Transpiler(new Lexer(), new Parser()));
        $builder->transpileMarkup($fixture);

        $portals = $builder->getPortals();
        $this->assertCount(3, $portals);
        $this->assertEquals(['one', 'two', 'three'], array_keys($portals));
        $this->assertInstanceOf(Portal::class, $portals['one']);
        $this->assertInstanceOf(Portal::class, $portals['two']);
        $this->assertInstanceOf(Portal::class, $portals['three']);
    }

    public function testTranspileMarkupGetLocationsWithExits()
    {
        $fixture = <<<END
        [LOCATION]
        id=one
        exits=doorFromOneToTwo
        [LOCATION]
        id=two
        exits=doorFromTwoToOne
        [PORTAL]
        id=doorFromOneToTwo
        destination=two
        [PORTAL]
        id=doorFromTwoToOne
        destination=one
        END;

        $builder = new SceneBuilder(new Transpiler(new Lexer(), new Parser()));
        $builder->transpileMarkup($fixture);

        $locations = $builder->getLocations();
        $this->assertCount(2, $locations);
        $this->assertEquals(['one', 'two'], array_keys($locations));
        $this->assertInstanceOf(Location::class, $locations['one']);
        $this->assertInstanceOf(Location::class, $locations['two']);

        $this->assertCount(1, $locations['one']->getExits());
        $this->assertCount(1, $locations['two']->getExits());
        $this->assertInstanceOf(Portal::class, $locations['one']->getExits()[0]);
        $this->assertInstanceOf(Portal::class, $locations['two']->getExits()[0]);

        $this->assertEquals('doorFromOneToTwo', $locations['one']->getExits()[0]->getId());
        $this->assertEquals('doorFromTwoToOne', $locations['two']->getExits()[0]->getId());
    }
}
