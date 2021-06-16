<?php

namespace AdventureGame\Test\Builder;

use AdventureGame\Builder\SceneBuilder;
use AdventureGame\Item\ItemInterface;
use AdventureGame\Location\Location;
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

    public function testTranspileMarkupFromRandomlyGeneratedMarkup()
    {
        $this->markTestIncomplete('todo: implement test');
    }
}
