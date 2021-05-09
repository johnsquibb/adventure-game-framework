<?php

namespace AdventureGame\Platform;

use AdventureGame\Character\Character;
use AdventureGame\Command\CommandController;
use AdventureGame\Command\CommandFactory;
use AdventureGame\Command\CommandParser;
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

/**
 * Provides factory and initialization for platform components.
 * Class PlatformFactory
 * @package AdventureGame\Platform
 */
class PlatformFactory
{
    private array $registry = [];

    public function createPlatformRegistry(): PlatformRegistry
    {
        $platformRegistry = new PlatformRegistry();

        $platformRegistry->outputController = $this->getOutputController();
        $platformRegistry->inputController = $this->getInputController();
        $platformRegistry->commandController = $this->getCommandController();
        $platformRegistry->gameController = $this->getGameController();
        $platformRegistry->mapController = $this->getMapController();
        $platformRegistry->playerController = $this->getPlayerController();

        return $platformRegistry;
    }

    private function getRegisteredObject(string $className): ?object
    {
        return $this->registry[$className] ?? null;
    }

    private function registerObject(object $object): void
    {
        $this->registry[$object::class] = $object;
    }

    private function getCommandParser(): CommandParser
    {
        // TODO load from configuration file.
        $verbs = ['north', 'take', 'look', 'put'];
        $nouns = ['sword', 'sheath', 'chest', 'test-container-item', 'test-item-in-container'];
        $articles = [];
        $prepositions = ['at', 'into', 'from'];
        $aliases = [];

        $object = $this->getRegisteredObject(CommandParser::class);
        if ($object === null) {
            $object = new CommandParser($verbs, $nouns, $articles, $prepositions, $aliases);
            $this->registerObject($object);
        }

        return $object;
    }

    private function getInputController(): InputController
    {
        $object = $this->getRegisteredObject(InputController::class);
        if ($object === null) {
            $object = new InputController($this->getCommandParser(), $this->getCommandController());
            $this->registerObject($object);
        }

        return $object;
    }

    private function getOutputController(): OutputController
    {
        $object = $this->getRegisteredObject(OutputController::class);
        if ($object === null) {
            $object = new OutputController();
            $this->registerObject($object);
        }

        return $object;
    }

    private function getGameController(): GameController
    {
        $object = $this->getRegisteredObject(GameController::class);
        if ($object === null) {
            $mapController = $this->getMapController();
            $playerController = $this->getPlayerController();
            $object = new GameController($mapController, $playerController);
            $this->registerObject($object);
        }

        return $object;
    }

    private function getCommandController(): CommandController
    {
        $object = $this->getRegisteredObject(CommandController::class);
        if ($object === null) {
            $object = new CommandController($this->getCommandFactory(), $this->getGameController());
            $this->registerObject($object);
        }

        return $object;
    }

    private function getCommandFactory(): CommandFactory
    {
        $object = $this->getRegisteredObject(CommandFactory::class);
        if ($object === null) {
            $object = new CommandFactory($this->getCommandParser(), $this->getOutputController());
            $this->registerObject($object);
        }

        return $object;
    }

    private function getMapController(): MapController
    {
        // TODO load from configuration file.
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

        $object = $this->getRegisteredObject(MapController::class);
        if ($object === null) {
            $object = new MapController($locations);
            // Spawn player in room 1
            $object->setPlayerLocationById($location1->id);
            $this->registerObject($object);
        }

        return $object;
    }

    private function getPlayerController(): PlayerController
    {
        // TODO load from configuration.
        $playerName = 'test-player';
        $inventory = new Container();

        $object = $this->getRegisteredObject(PlayerController::class);
        if ($object === null) {
            $player = new Character($playerName, $inventory);
            $object = new PlayerController($player);
            $this->registerObject($object);
        }

        return $object;
    }
}