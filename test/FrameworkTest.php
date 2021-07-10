<?php

namespace AdventureGame\Test;

use AdventureGame\Character\Character;
use AdventureGame\Command\CommandController;
use AdventureGame\Command\CommandFactory;
use AdventureGame\Command\CommandParser;
use AdventureGame\Event\EventController;
use AdventureGame\Game\GameController;
use AdventureGame\Game\MapController;
use AdventureGame\Game\PlayerController;
use AdventureGame\IO\InputController;
use AdventureGame\IO\OutputController;
use AdventureGame\Item\Container;
use AdventureGame\Item\ContainerItem;
use AdventureGame\Item\Item;
use AdventureGame\Location\Location;
use AdventureGame\Location\Portal;
use PHPUnit\Framework\TestCase;

abstract class FrameworkTest extends TestCase
{
    private function createEventController(): EventController
    {
        return new EventController();
    }

    protected function createInputController(): InputController
    {
        $commandParser = $this->createCommandParser();
        $commandController = $this->createCommandController();
        $gameController = $this->createGameController();

        return new InputController($commandParser, $commandController, $gameController);
    }

    protected function createCommandParser(): CommandParser
    {
        $verbs = ['north', 'take', 'look', 'put'];
        $nouns = [
            'sword',
            'sheath',
            'chest',
            'test-container-item',
            'test-item-in-container',
            'item',
            'container'
        ];
        $articles = [];
        $prepositions = ['at', 'into', 'from'];
        $aliases = [];

        return new CommandParser($verbs, $nouns, $articles, $prepositions, $aliases, [], [], []);
    }

    protected function createCommandController(): CommandController
    {
        $gameController = $this->createGameController();
        $commandParser = $this->createCommandParser();
        $outputController = $this->createOutputController();
        $commandFactory = new CommandFactory($commandParser, $outputController);
        return new CommandController($commandFactory, $gameController);
    }

    protected function createGameController(): GameController
    {
        $mapController = $this->createMapController();
        $playerController = $this->createPlayerController();
        $eventController = $this->createEventController();

        return new GameController($mapController, $playerController, $eventController);
    }

    protected function createMapController(): MapController
    {
        $container = new Container();

        $item = new Item(
            'test-item-1',
            'Test Item 1',
            ['Test Item 1 description'],
            ['test']
        );
        $item->setDiscovered(true);
        $container->addItem($item);

        $containerItem = new ContainerItem(
            'test-container-item',
            'Test Container Item',
            ['Test container item description'],
            ['container', 'test-container-item'],
        );
        $container->addItem($containerItem);

        $item = new Item(
            'test-item-2',
            'Test Item 2',
            ['Test Item 2 description'],
            ['item','test-item-in-container']
        );
        $item->setDiscovered(true);
        $containerItem->addItem($item);

        $item = new Item(
            'test-item-3',
            'Test Item 3',
            ['Test Item 3 description'],
            ['test-item-2-in-container']
        );
        $item->setDiscovered(true);
        $containerItem->addItem($item);

        $door1 = new Portal(
            'test-door',
            'Wooden Door',
            ['A door leading to the east'],
            ['east'],
            'east',
            'test-room-2'
        );
        $location1 = new Location(
            'test-room-1',
            'Test Room 1',
            ['This is a test room.'],
            $container,
            [$door1],
        );

        $door2 = new Portal(
            'test-door',
            '',
            [],
            ['west'],
            'west',
            'test-room-1'
        );
        $location2 = new Location(
            'test-room-2',
            'Test Room 2',
            ['This is another test room.'],
            new Container(),
            [$door2],
        );

        $locations = [$location1, $location2];

        // Spawn player in room 1
        $mapController = new MapController($locations);
        $mapController->setPlayerLocationById($location1->getId());

        return $mapController;
    }

    protected function createPlayerController(): PlayerController
    {
        $player = new Character('test-player', new Container());

        return new PlayerController($player);
    }

    protected function createOutputController(): OutputController
    {
        return new OutputController();
    }
}
