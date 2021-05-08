<?php

namespace AdventureGame\Test;

use AdventureGame\Character\Character;
use AdventureGame\Command\CommandParser;
use AdventureGame\Game\GameController;
use AdventureGame\Game\MapController;
use AdventureGame\Game\PlayerController;
use AdventureGame\IO\OutputController;
use AdventureGame\Item\Container;
use AdventureGame\Item\ContainerItem;
use AdventureGame\Item\Item;
use AdventureGame\Location\Location;
use AdventureGame\Location\Portal;
use PHPUnit\Framework\TestCase;

abstract class FrameworkTest extends TestCase
{
    protected function createOutputController(): OutputController
    {
        return new OutputController();
    }

    protected function createCommandParser(): CommandParser
    {
        $verbs = ['north', 'take', 'look', 'put'];
        $nouns = ['sword', 'sheath', 'chest'];
        $articles = [];
        $prepositions = ['at', 'into', 'from'];
        $aliases = [];

        return new CommandParser($verbs, $nouns, $articles, $prepositions, $aliases);
    }

    protected function createMapController(): MapController
    {
        $container = new Container();
        $container->addItem(
            new Item(
                'test-item-1',
                'Test Item 1',
                'Test Item 1 description',
                'test'
            )
        );

        $containerItem = new ContainerItem(
            'test-container-item',
            'Test Container Item',
            'Test container item description',
            'test-container-item',
        );
        $container->addItem($containerItem);
        $containerItem->addItem(
            new Item(
                'test-item-2',
                'Test Item 2',
                'Test Item 2 description',
                'test-item-in-container'
            )
        );
        $containerItem->addItem(
            new Item(
                'test-item-3',
                'Test Item 3',
                'Test Item 3 description',
                'test-item-2-in-container'
            )
        );

        $door1 = new Portal('test-door', 'east', 'test-room-2');
        $location1 = new Location(
            'test-room-1',
            'Test Room 1',
            'This is a test room.',
            $container,
            [$door1],
        );

        $door2 = new Portal('test-door', 'west', 'test-room-1');
        $location2 = new Location(
            'test-room-2',
            'Test Room 2',
            'This is another test room.',
            new Container(),
            [$door2],
        );

        $locations = [$location1, $location2];

        // Spawn player in room 1
        $mapController = new MapController($locations);
        $mapController->setPlayerLocationById($location1->id);

        return $mapController;
    }

    protected function createPlayerController(): PlayerController
    {
        $player = new Character('test-player', new Container());

        return new PlayerController($player);
    }

    protected function createGameController(): GameController
    {
        $mapController = $this->createMapController();
        $playerController = $this->createPlayerController();

        return new GameController($mapController, $playerController);
    }
}