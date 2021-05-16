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

    private function getOutputController(): OutputController
    {
        $object = $this->getRegisteredObject(OutputController::class);
        if ($object === null) {
            $object = new OutputController();
            $this->registerObject($object);
        }

        return $object;
    }

    private function getRegisteredObject(string $className): ?object
    {
        return $this->registry[$className] ?? null;
    }

    private function registerObject(object $object): void
    {
        $this->registry[$object::class] = $object;
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

    private function getCommandParser(): CommandParser
    {
        // TODO load from configuration file.
        $verbs = [
            'go',
            'take',
            'drop',
            'look',
            'put',
            'lock',
            'unlock',
        ];
        $nouns = [
            'sword',
            'chest',
            'door',
            'potion',
            'key',
            'north',
            'east',
            'south',
            'west',
        ];
        $articles = ['the'];
        $prepositions = ['at', 'inside', 'into', 'from', 'with'];
        $aliases = [
            'move' => 'go',
        ];
        $substitutions = [
            'n' => 'go north',
            'e' => 'go east',
            's' => 'go south',
            'w' => 'go west',
            'north' => 'go north',
            'east' => 'go east',
            'south' => 'go south',
            'west' => 'go west',
        ];

        $object = $this->getRegisteredObject(CommandParser::class);
        if ($object === null) {
            $object = new CommandParser(
                $verbs,
                $nouns,
                $articles,
                $prepositions,
                $aliases,
                $substitutions
            );
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

    private function getMapController(): MapController
    {
        // TODO load from configuration file.

        $chest = new ContainerItem(
            'treasure-chest-1',
            'Treasure Chest',
            'A chest containing valuable treasure.',
            'chest',
        );

        $swordOfPoking = new Item(
            'sword-of-poking',
            'The Sword of Poking',
            'An average sword, made for poking aggressive beasts.',
            'sword',
        );
        $potionOfHealing1 = new Item(
            'potion-of-healing-1',
            'Potion of Healing I',
            'A potion that restores life.',
            'potion',
        );
        $keyToDoorWoodenDoor = new Item(
            'keyToWoodenDoor',
            'Key to Wooden Door',
            'A metal key that unlocks the wooden door at spawn.',
            'key'
        );

        $chest->addItem($swordOfPoking);
        $chest->addItem($potionOfHealing1);
        $chest->addItem($keyToDoorWoodenDoor);

        $doorFromSpawnToEastRoom = new Portal(
            'door-from-spawn-to-east-room',
            'Wooden Door',
            'A heavy wooden door leading back to spawn.',
            'door',
            'east', 'room-east-of-spawn'
        );

        $doorFromSpawnToEastRoom->setMutable(true);
        $doorFromSpawnToEastRoom->setLocked(true);
        $doorFromSpawnToEastRoom->setKeyEntityId($keyToDoorWoodenDoor->getId());

        $spawnRoom = new Location(
            'spawn',
            'Player Spawn',
            'This is the starting room.',
            new Container(),
            [$doorFromSpawnToEastRoom],
        );
        $spawnRoom->getContainer()->addItem($chest);

        $doorFromEastRoomToSpawn = new Portal(
            'door-from-east-room-to-spawn',
            'Wooden Door',
            'A heavy wooden door leading to the east.',
            'door',
            'west', 'spawn'
        );
        $roomEastOfSpawn = new Location(
            'room-east-of-spawn',
            'Room East of Spawn',
            'There is nothing special about this room. It is just an ordinary room with walls.',
            new Container(),
            [$doorFromEastRoomToSpawn],
        );

        $locations = [$spawnRoom, $roomEastOfSpawn];

        $object = $this->getRegisteredObject(MapController::class);
        if ($object === null) {
            $object = new MapController($locations);

            // Spawn player in room 1
            $object->setPlayerLocationById($spawnRoom->getId());
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