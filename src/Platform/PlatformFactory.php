<?php

namespace AdventureGame\Platform;

use AdventureGame\Character\Character;
use AdventureGame\Command\CommandController;
use AdventureGame\Command\CommandFactory;
use AdventureGame\Command\CommandParser;
use AdventureGame\Event\AbstractInventoryEvent;
use AdventureGame\Event\EventController;
use AdventureGame\Event\Events\ActivateItemEvent;
use AdventureGame\Event\Events\DropItemEvent;
use AdventureGame\Event\Events\EnterLocationEvent;
use AdventureGame\Event\Events\ExitLocationEvent;
use AdventureGame\Event\Events\TakeItemEvent;
use AdventureGame\Event\Triggers\AddItemToInventoryTrigger;
use AdventureGame\Event\Triggers\AddItemToLocationTrigger;
use AdventureGame\Event\Triggers\DropItemFromInventoryTrigger;
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
            'inventory',
            'activate',
        ];

        $nouns = [
            'sword',
            'manual',
            'chest',
            'door',
            'flashlight',
            'key',
            'north',
            'east',
            'south',
            'west',
            'up',
            'down',
            'reward',
            'reward.exit',
            'reward.enter',
            'key.keyToCellarDoor',
            'key.keyToWoodenDoor',
            'map',
        ];

        $articles = ['a', 'an', 'the'];

        $prepositions = ['at', 'inside', 'in', 'into', 'from', 'with'];

        $aliases = [
            'move' => 'go',
            'ex' => 'look',
            'examine' => 'look',
            'i' => 'inventory',
        ];

        $phrases = [
            'exit reward' => 'reward.exit',
            'enter reward' => 'reward.enter',
            'key to cellar door' => 'key.keyToCellarDoor',
            'key to wooden door' => 'key.keyToWoodenDoor',
        ];

        $shortcuts = [
            'n' => 'go north',
            'e' => 'go east',
            's' => 'go south',
            'w' => 'go west',
            'north' => 'go north',
            'east' => 'go east',
            'south' => 'go south',
            'west' => 'go west',
            'down' => 'go down',
            'up' => 'go up',
        ];

        $object = $this->getRegisteredObject(CommandParser::class);
        if ($object === null) {
            $object = new CommandParser(
                $verbs,
                $nouns,
                $articles,
                $prepositions,
                $aliases,
                $shortcuts,
                $phrases
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
            $eventController = $this->getEventController();
            $object = new GameController($mapController, $playerController, $eventController);
            $this->registerObject($object);
        }

        $object->setSaveDirectory($this->saveGameDirectory);

        return $object;
    }

    /**
     * Get the map controller with all its locations and items initialized.
     * @return MapController
     * @throws InvalidSaveDirectoryException
     */
    private function getMapController(): MapController
    {
        // TODO load from configuration file.

        $chest = new ContainerItem(
            'treasureChest',
            'Treasure Chest',
            'A chest containing valuable treasure.',
            ['chest'],
        );
        $chest->setAcquirable(false);

        $swordOfPoking = new Item(
            'swordOfPoking',
            'The Sword of Poking',
            'An average sword, made for poking aggressive beasts.',
            ['sword'],
        );
        $flashlight = new Item(
            'flashlight',
            'Flashlight',
            'A battery powered flashlight.',
            ['flashlight'],
        );
        $keyToWoodenDoor = new Item(
            'keyToWoodenDoor',
            'Key to Wooden Door',
            'A metal key that unlocks the wooden door at spawn.',
            ['key to wooden door', 'key.keyToWoodenDoor', 'key']
        );

        $chest->addItem($swordOfPoking);
        $chest->addItem($flashlight);
        $chest->addItem($keyToWoodenDoor);

        $doorFromSpawnToWestRoom = new Portal(
            'doorFromSpawnToWestRoom',
            'Wooden Door',
            'A heavy wooden door leading to the west.',
            ['door'],
            'west', 'roomWestOfSpawn'
        );

        $doorFromSpawnToWestRoom->setMutable(true);
        $doorFromSpawnToWestRoom->setLocked(true);
        $doorFromSpawnToWestRoom->setKeyEntityId($keyToWoodenDoor->getId());

        $doorFromWestRoomToSpawn = new Portal(
            'doorFromWestRoomToSpawn',
            'Wooden Door',
            'A heavy wooden door leading back to spawn.',
            ['door'],
            'east', 'spawn'
        );

        $doorFromWestRoomToSpawn->setKeyEntityId($keyToWoodenDoor->getId());

        $roomWestOfSpawn = new Location(
            'roomWestOfSpawn',
            'Room West of Spawn',
            'There is nothing special about this room. It is just an ordinary room with walls.',
            new Container(),
            [$doorFromWestRoomToSpawn],
        );

        $entryFromSpawnToHallway = new Portal(
            'entryFromSpawnToHallway',
            'Hallway Entrance',
            'An entrance to a hallway leading south.',
            ['hallway'],
            'south', 'hallwayLeadingSouthFromSpawn'
        );

        $entryFromHallwayToSpawn = new Portal(
            'entryFromSpawnToHallway',
            'Hallway Entrance',
            'An entrance to a hallway leading north.',
            ['hallway'],
            'north', 'spawn'
        );

        $doorFromHallwayToCourtyard = new Portal(
            'doorFromHallwayToCourtyard',
            'Front door',
            'A door with a window, through which you can see an exterior courtyard.',
            ['door'],
            'south', 'courtyard'
        );

        $keyToCellarDoor = new Item(
            'keyToCellarDoor',
            'Key to Cellar Door',
            'A small key that unlocks the door to the cellar.',
            ['key to cellar door', 'key.keyToCellarDoor', 'key']
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
            ['door'],
            'north',
            'hallwayLeadingSouthFromSpawn'
        );

        $pathFromCourtyardToTown = new Portal(
            'pathFromCourtyardToTown',
            'Path to Town',
            "A light walking path that leads south into the distance toward town.",
            ['path'],
            'south',
            'houseInTown'
        );

        $stepsFromCourtyardToShed = new Portal(
            'stepsFromCourtyardToShed',
            'Steps Leading Down',
            "Stone steps leading down to an open clearing with a small shed.",
            ['steps'],
            'down',
            'smallShed'
        );

        $courtyard = new Location(
            'courtyard',
            'Courtyard',
            'A courtyard surrounds the entrance of the house. ' . "\n" .
            'Hedges form a wall in three directions, with a path leading away from the house toward town.',
            new Container(),
            [$doorFromCourtyardToHallway, $pathFromCourtyardToTown, $stepsFromCourtyardToShed]
        );

        $pathFromTownToCourtyard = new Portal(
            'pathFromTownToCourtyard',
            'Path from Town',
            "A light walking path that leads north away from town.",
            ['path'],
            'north',
            'courtyard'
        );

        $houseInTown = new Location(
            "houseInTown",
            "The House",
            "A house belonging to someone. They don't appear to be home.",
            new Container(),
            [$pathFromTownToCourtyard]
        );

        $stepsFromShedToCourtyard = new Portal(
            'stepsFromShedToCourtyard',
            'Steps Leading Up',
            "Stone steps leading up to a courtyard.",
            ['steps'],
            'up',
            'courtyard'
        );

        $cellarDoorLeadingIn = new Portal(
            'cellarDoor',
            'Door to Cellar',
            "A door leading down into a cellar.",
            ['door'],
            'down',
            'cellar'
        );
        $cellarDoorLeadingIn->setMutable(true);
        $cellarDoorLeadingIn->setLocked(true);
        $cellarDoorLeadingIn->setKeyEntityId($keyToCellarDoor->getId());

        $smallShed = new Location(
            "smallShed",
            "A small shed",
            "A small shed with weathered siding and a small window.",
            new Container(),
            [$stepsFromShedToCourtyard, $cellarDoorLeadingIn]
        );

        $cellarDoorLeadingOut = new Portal(
            'cellarDoor',
            'Cellar Door',
            "The way out of the cellar.",
            ['door'],
            'up',
            'smallShed'
        );

        $cellar = new Location(
            "cellar",
            "Cellar",
            "A dark cellar with a low ceiling. It is difficult to see anything without some kind of light.",
            new Container(),
            [$cellarDoorLeadingOut]
        );

        $spawnRoom = new Location(
            'spawn',
            'Player Spawn',
            'This is the starting room.',
            new Container(),
            [$doorFromSpawnToWestRoom, $entryFromSpawnToHallway],
        );
        $spawnRoom->getContainer()->addItem($chest);

        $object = $this->getRegisteredObject(MapController::class);
        if ($object === null) {
            $object = new MapController(
                [
                    $spawnRoom,
                    $roomWestOfSpawn,
                    $hallwayLeadingSouth,
                    $courtyard,
                    $smallShed,
                    $houseInTown,
                    $cellar,
                ]
            );

            // Spawn player in room 1
            $object->setPlayerLocationById($spawnRoom->getId());
            $this->registerObject($object);

            $gameController = $this->getGameController();

            // Add the owner's manual to inventory when taking sword.
            $swordOfPokingOwnersManual = new Item(
                'swordOfPokingOwnersManual',
                'Sword of Poking Owner\'s Manual',
                'Your guide to all matters related to the sword of poking. Use it in good health.',
                ['manual']
            );

            $trigger = new AddItemToInventoryTrigger($swordOfPokingOwnersManual);
            $event = new TakeItemEvent($trigger, 'swordOfPoking', 'spawn');
            $gameController->eventController->addEvent($event);

            // Drop the sword when dropping the owner's manual from inventory.
            $trigger = new DropItemFromInventoryTrigger($swordOfPoking->getId());
            $event = new DropItemEvent($trigger, 'swordOfPokingOwnersManual', '*');
            $gameController->eventController->addEvent($event);

            // Give the player a reward for entering the west room.
            $enteredWestRoomReward = new Item(
                'enteredWestRoomReward',
                'Reward for Entering West Room',
                'You did it! You made it into the west room. This reward is proof of your achievement.',
                ['enter reward', 'reward.enter', 'reward']
            );

            $trigger = new AddItemToInventoryTrigger($enteredWestRoomReward);
            $event = new EnterLocationEvent($trigger, 'roomWestOfSpawn');
            $gameController->eventController->addEvent($event);

            // Give the player a reward for exiting the west room.
            $exitedWestRoomReward = new Item(
                'exitedWestRoomReward',
                'Reward for Exiting West Room',
                'Great job getting out of the west room! This reward is proof of your achievement.',
                ['exit reward', 'reward.exit', 'reward']
            );

            $trigger = new AddItemToInventoryTrigger($exitedWestRoomReward);
            $event = new ExitLocationEvent($trigger, 'roomWestOfSpawn');
            $gameController->eventController->addEvent($event);

            // When the player turns the flashlight on in the cellar, reveal the map to the secret room.
            $mapToSecretRoom = new Item(
                'mapToSecretRoom',
                'Map to Secret Room',
                'A map detailing the location of a secret room. A speakable word is written on the map.',
                ['map']
            );
            $trigger = new AddItemToLocationTrigger($mapToSecretRoom);
            $event = new ActivateItemEvent($trigger, 'flashlight', 'cellar');
            $gameController->eventController->addEvent($event);
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

    /**
     * Get the event controller.
     * @return EventController
     */
    private function getEventController(): EventController
    {
        $object = $this->getRegisteredObject(EventController::class);
        if ($object === null) {
            $object = new EventController();
            $this->registerObject($object);
        }

        return $object;
    }
}