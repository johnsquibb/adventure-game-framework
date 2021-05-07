<?php

namespace AdventureGame\Command\Commands;

use AdventureGame\Character\Character;
use AdventureGame\Game\GameController;
use AdventureGame\Game\MapController;
use AdventureGame\Game\PlayerController;
use AdventureGame\IO\OutputController;
use AdventureGame\Item\Container;
use AdventureGame\Item\Item;
use AdventureGame\Location\Location;
use AdventureGame\Location\Portal;
use PHPUnit\Framework\TestCase;

abstract class CommandTest extends TestCase
{
    protected function createOutputController(): OutputController
    {
        return new OutputController();
    }

    protected function createMapController(): MapController
    {
        $container = new Container();
        $item = new Item(
            'test-item',
            'Test Item',
            'Test Item Description',
            'test'
        );
        $container->addItem($item);

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