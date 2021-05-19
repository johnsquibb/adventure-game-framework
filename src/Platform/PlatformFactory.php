<?php

namespace AdventureGame\Platform;

use AdventureGame\Character\Character;
use AdventureGame\Command\CommandController;
use AdventureGame\Command\CommandFactory;
use AdventureGame\Command\CommandParser;
use AdventureGame\Game\Exception\InvalidSaveDirectoryException;
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
 * Class PlatformFactory provides factory and initialization for platform components.
 * @package AdventureGame\Platform
 */
class PlatformFactory
{
    private array $registry = [];

    public function __construct(private string $saveGameDirectory)
    {
    }

    /**
     * Initialize the registry and all its dependencies to ready a new game.
     * @return PlatformRegistry
     * @throws InvalidSaveDirectoryException
     */
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

    /**
     * Get a previously registered object. Ensures a singleton until the registry cache is cleared.
     * @param string $className
     * @return object|null
     */
    private function getRegisteredObject(string $className): ?object
    {
        return $this->registry[$className] ?? null;
    }

    /**
     * Register an object to ensure a singleton until the registry cache is cleared.
     * @param object $object
     */
    private function registerObject(object $object): void
    {
        $this->registry[$object::class] = $object;
    }

    /**
     * Clear the registry cache. This is important when starting a new game to ensure fresh game
     * objects are loaded.
     */
    public function clearRegistry()
    {
        $this->registry = [];
    }

    /**
     * Get the input controller.
     * @return InputController
     * @throws InvalidSaveDirectoryException
     */
    private function getInputController(): InputController
    {
        $object = $this->getRegisteredObject(InputController::class);
        if ($object === null) {
            $object = new InputController($this->getCommandParser(), $this->getCommandController());
            $this->registerObject($object);
        }

        return $object;
    }

    /**
     * Get the output controller.
     * @return OutputController
     */
    private function getOutputController(): OutputController
    {
        $object = $this->getRegisteredObject(OutputController::class);
        if ($object === null) {
            $object = new OutputController();
            $this->registerObject($object);
        }

        return $object;
    }

    /**
     * Get the command parser with initialized vocabulary.
     * @return CommandParser
     */
    private function getCommandParser(): CommandParser
    {
        // TODO load from configuration file.
        $verbs = [
            'save',
            'load',
            'quit',
            'new',
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

    /**
     * Get the command controller.
     * @return CommandController
     * @throws InvalidSaveDirectoryException
     */
    private function getCommandController(): CommandController
    {
        $object = $this->getRegisteredObject(CommandController::class);
        if ($object === null) {
            $object = new CommandController($this->getCommandFactory(), $this->getGameController());
            $this->registerObject($object);
        }

        return $object;
    }

    /**
     * Get the command factory.
     * @return CommandFactory
     */
    private function getCommandFactory(): CommandFactory
    {
        $object = $this->getRegisteredObject(CommandFactory::class);
        if ($object === null) {
            $object = new CommandFactory($this->getCommandParser(), $this->getOutputController());
            $this->registerObject($object);
        }

        return $object;
    }

    /**
     * Get the game controller.
     * @return GameController
     * @throws InvalidSaveDirectoryException
     */
    private function getGameController(): GameController
    {
        $object = $this->getRegisteredObject(GameController::class);
        if ($object === null) {
            $mapController = $this->getMapController();
            $playerController = $this->getPlayerController();
            $object = new GameController($mapController, $playerController);
            $this->registerObject($object);
        }

        $object->setSaveDirectory($this->saveGameDirectory);

        return $object;
    }

    /**
     * Get the map controller with all its locations and items initialized.
     * @return MapController
     */
    private function getMapController(): MapController
    {
        // TODO load from configuration file.

        $chest = new ContainerItem(
            'treasureChest',
            'Treasure Chest',
            'A chest containing valuable treasure.',
            'chest',
        );

        $swordOfPoking = new Item(
            'swordOfPoking',
            'The Sword of Poking',
            'An average sword, made for poking aggressive beasts.',
            'sword',
        );
        $potionOfHealing1 = new Item(
            'potionOfHealing1',
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
            'doorFromSpawnToEastRoom',
            'Wooden Door',
            'A heavy wooden door leading to the east.',
            'door',
            'east', 'roomEastOfSpawn'
        );

        $doorFromSpawnToEastRoom->setMutable(true);
        $doorFromSpawnToEastRoom->setLocked(true);
        $doorFromSpawnToEastRoom->setKeyEntityId($keyToDoorWoodenDoor->getId());

        $doorFromEastRoomToSpawn = new Portal(
            'doorFromEastRoomToSpawn',
            'Wooden Door',
            'A heavy wooden door leading back to spawn.',
            'door',
            'west', 'spawn'
        );

        $doorFromEastRoomToSpawn->setKeyEntityId($keyToDoorWoodenDoor->getId());

        $roomEastOfSpawn = new Location(
            'roomEastOfSpawn',
            'Room East of Spawn',
            'There is nothing special about this room. It is just an ordinary room with walls.',
            new Container(),
            [$doorFromEastRoomToSpawn],
        );

        $entryFromSpawnToHallway = new Portal(
            'entryFromSpawnToHallway',
            'Hallway Entrance',
            'An entrance to a hallway leading south.',
            'hallway',
            'south', 'hallwayLeadingSouthFromSpawn'
        );

        $entryFromHallwayToSpawn = new Portal(
            'entryFromSpawnToHallway',
            'Hallway Entrance',
            'An entrance to a hallway leading north.',
            'hallway',
            'north', 'spawn'
        );

        $doorFromHallwayToCourtyard = new Portal(
            'doorFromHallwayToCourtyard',
            'Front door',
            'A door with a window, through which you can see an exterior courtyard.',
            'door',
            'south', 'courtyard'
        );

        $keyToCellarDoor = new Item(
            'keyToCellarDoor',
            'Key to Cellar',
            'A small key that unlocks the door to the cellar.',
            'key'
        );

        $hallwayLeadingSouth = new Location(
            'hallwayLeadingSouthFromSpawn',
            'Hallway Leading South',
            'A hallway that leads south from spawn with a single exit to exterior courtyard',
            new Container(),
            [$doorFromHallwayToCourtyard, $entryFromHallwayToSpawn]
        );
        $hallwayLeadingSouth->getContainer()->addItem($keyToCellarDoor);

        $doorFromCourtyardToHallway = new Portal(
            'doorFromCourtyardToHallway',
            'Front door',
            "A door that leads inside the house. It has a small stained glass window in the center.",
            'door',
            'north', 'hallwayLeadingSouthFromSpawn'
        );

        $courtyard = new Location(
            'courtyard',
            'Courtyard',
            'A courtyard surrounds the entrance of the house. ' . "\n" .
            'Hedges form a wall in three directions, with a path leading away from the house toward town.',
            new Container(),
            [$doorFromCourtyardToHallway]
        );

        $spawnRoom = new Location(
            'spawn',
            'Player Spawn',
            'This is the starting room.',
            new Container(),
            [$doorFromSpawnToEastRoom, $entryFromSpawnToHallway],
        );
        $spawnRoom->getContainer()->addItem($chest);

        $object = $this->getRegisteredObject(MapController::class);
        if ($object === null) {
            $object = new MapController(
                [
                    $spawnRoom,
                    $roomEastOfSpawn,
                    $hallwayLeadingSouth,
                    $courtyard,
                ]
            );

            // Spawn player in room 1
            $object->setPlayerLocationById($spawnRoom->getId());
            $this->registerObject($object);
        }

        return $object;
    }

    /**
     * Get the player controller with the player character initialized.
     * @return PlayerController
     */
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