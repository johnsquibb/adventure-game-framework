<?php

namespace AdventureGame\Platform;

use AdventureGame\Character\Character;
use AdventureGame\Command\CommandController;
use AdventureGame\Command\CommandFactory;
use AdventureGame\Command\CommandParser;
use AdventureGame\Event\EventController;
use AdventureGame\Event\Events\ActivateItemEvent;
use AdventureGame\Event\Events\DeactivateItemEvent;
use AdventureGame\Event\Events\EnterLocationEvent;
use AdventureGame\Event\Events\HasActivatedItemEvent;
use AdventureGame\Event\Triggers\AddItemToInventoryUseTrigger;
use AdventureGame\Event\Triggers\AddItemToLocationUseTrigger;
use AdventureGame\Event\Triggers\AddLocationToMapUseTrigger;
use AdventureGame\Event\Triggers\ItemComparison;
use AdventureGame\Event\Triggers\MultipleActivatorPortalLockTrigger;
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
     * Clear the registry cache. This is important when starting a new game to ensure fresh game
     * objects are loaded.
     */
    public function clearRegistry()
    {
        $this->registry = [];
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
            'read',
            'lock',
            'unlock',
            'inventory',
            'activate',
            'deactivate',
        ];

        $nouns = [
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
            'key.keyToWoodenDoor',
            'map',
            'letter',
            'switch.one',
            'switch.two',
            'switch.three',
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
            'key to wooden door' => 'key.keyToWoodenDoor',
            'turn on flashlight' => 'activate flashlight',
            'turn flashlight on' => 'activate flashlight',
            'turn off flashlight' => 'deactivate flashlight',
            'turn flashlight off' => 'deactivate flashlight',
            'read map' => 'activate map',
            'look at map' => 'activate map',
            'read secret letter' => 'read letter',
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
        $object = $this->getRegisteredObject(MapController::class);
        if ($object instanceof MapController) {
            return $object;
        }

        // TODO load from configuration file.

        $chest = new ContainerItem(
            'treasureChest',
            'Treasure Chest',
            'A chest containing valuable treasure.',
            ['chest'],
        );
        $chest->setAcquirable(false);

        $flashlight = new Item(
            'flashlight',
            'Flashlight',
            'A battery powered flashlight.',
            ['flashlight'],
        );
        $flashlight->setActivatable(true);
        $flashlight->setDeactivatable(true);
        $keyToWoodenDoor = new Item(
            'keyToWoodenDoor',
            'Key to Wooden Door',
            'A metal key that unlocks the wooden door at spawn.',
            ['key to wooden door', 'key.keyToWoodenDoor', 'key']
        );

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

        $doorFromWestRoomToSecretRoom = new Portal(
            'doorFromWestRoomToSecretRoom',
            'Secret Door',
            'A secret door has been revealed to the west.',
            ['door'],
            'west', 'secretRoom'
        );

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

        $hallwayLeadingSouth = new Location(
            'hallwayLeadingSouthFromSpawn',
            'Hallway Leading South',
            'A hallway that leads south from spawn with a single exit to exterior courtyard',
            new Container(),
            [$doorFromHallwayToCourtyard, $entryFromHallwayToSpawn]
        );

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

        $cellarDoorLeadingIn = new Portal(
            'cellarDoor',
            'Door to Cellar',
            "A door leading down into a cellar.",
            ['door'],
            'down',
            'cellar'
        );
        $cellarDoorLeadingIn->setMutable(true);

        // The cellar door will be unlocked by activating a switch in the House location.
        $cellarDoorLeadingIn->setLocked(true);

        $cellarDoorLeadingOut = new Portal(
            'cellarDoor',
            'Cellar Door',
            "The way out of the cellar.",
            ['door'],
            'up',
            'houseInTown'
        );

        $houseInTown = new Location(
            "houseInTown",
            "The House",
            "A house belonging to someone. They don't appear to be home.",
            new Container(),
            [$pathFromTownToCourtyard, $cellarDoorLeadingIn]
        );

        $switch1 = new Item(
            'switch1',
            'Switch 1',
            "There's no telling what this switch does.",
            ['switch.one']
        );
        $switch1->setActivatable(true);
        $switch1->setDeactivatable(true);
        $switch1->setAcquirable(false);
        $houseInTown->getContainer()->addItem($switch1);

        $switch2 = new Item(
            'switch2',
            'Switch 2',
            "There's no telling what this switch does.",
            ['switch.two']
        );
        $switch2->setActivatable(true);
        $switch2->setDeactivatable(true);
        $switch2->setAcquirable(false);
        $houseInTown->getContainer()->addItem($switch2);

        $switch3 = new Item(
            'switch3',
            'Switch 3',
            "There's no telling what this switch does.",
            ['switch.three']
        );
        $switch3->setActivatable(true);
        $switch3->setDeactivatable(true);
        $switch3->setAcquirable(false);
        $houseInTown->getContainer()->addItem($switch3);

        $activators = [$switch1, $switch2, $switch3];

        $comp1 = new ItemComparison(true);
        $comp2 = new ItemComparison(false);
        $comp3 = new ItemComparison(true);

        $comparisons = [$comp1, $comp2, $comp3];

        $stepsFromShedToCourtyard = new Portal(
            'stepsFromShedToCourtyard',
            'Steps Leading Up',
            "Stone steps leading up to a courtyard.",
            ['steps'],
            'up',
            'courtyard'
        );

        $smallShed = new Location(
            "smallShed",
            "A small shed",
            "A small shed with weathered siding and a small window.",
            new Container(),
            [$stepsFromShedToCourtyard, $cellarDoorLeadingIn]
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

        $doorFromSecretRoomToWestRoom = new Portal(
            'doorFromWestRoomToSecretRoom',
            'Secret Door',
            'Exit to the east',
            ['door'],
            'east', 'roomWestOfSpawn'
        );

        $secretRoom = new Location(
            'secretRoom',
            'The Secret Room',
            'You have discovered a secret room.',
            new Container(),
            [$doorFromSecretRoomToWestRoom],
        );

        $secretLetter = new Item(
            'secretLetter',
            'A Secret Letter',
            'A folded letter written on old paper.',
            ['secret letter', 'letter']
        );
        $secretLetter->setReadable(true);
        $secretLetter->setLines(
            [
                'Hello Adventurer!',
                '',
                'You have found my secret room, and have thus won the game!',
                'I hope you have enjoyed this sample adventure.',
                'Now, go forth, and create your own using the framework provided!',
                '',
                'Sincerely,',
                'The Powerful Mage',
            ]
        );
        $secretRoom->getContainer()->addItem($secretLetter);

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

        // When the player activates the correct sequence of switches in house, unlock the cellar door.
        $trigger = new MultipleActivatorPortalLockTrigger(
            $activators,
            $comparisons,
            $cellarDoorLeadingIn
        );
        $gameController->getEventController()->addEvent(
            new ActivateItemEvent($trigger, $switch1->getId(), $houseInTown->getId())
        );
        $gameController->getEventController()->addEvent(
            new DeactivateItemEvent($trigger, $switch1->getId(), $houseInTown->getId())
        );
        $gameController->getEventController()->addEvent(
            new ActivateItemEvent($trigger, $switch2->getId(), $houseInTown->getId())
        );
        $gameController->getEventController()->addEvent(
            new DeactivateItemEvent($trigger, $switch2->getId(), $houseInTown->getId())
        );
        $gameController->getEventController()->addEvent(
            new ActivateItemEvent($trigger, $switch3->getId(), $houseInTown->getId())
        );
        $gameController->getEventController()->addEvent(
            new DeactivateItemEvent($trigger, $switch3->getId(), $houseInTown->getId())
        );

        // When the player turns the flashlight on in the cellar, reveal the map to the secret room.
        $mapToSecretRoom = new Item(
            'mapToSecretRoom',
            'Map to Secret Room',
            'A map detailing the location of a secret room. A speakable word is written on the map.',
            ['map']
        );
        $mapToSecretRoom->setActivatable(true);
        $trigger = new AddItemToLocationUseTrigger($mapToSecretRoom);
        $event = new ActivateItemEvent($trigger, 'flashlight', 'cellar');
        $gameController->getEventController()->addEvent($event);

        // Apply the same trigger on room entry when the flashlight is already activated.
        $event = new HasActivatedItemEvent($trigger, 'flashlight', 'cellar');
        $gameController->getEventController()->addEvent($event);

        // When reading the map, add the secret room as a new location.
        $trigger = new AddLocationToMapUseTrigger($secretRoom);
        $trigger->addEntrance('roomWestOfSpawn', $doorFromWestRoomToSecretRoom);
        $event = new ActivateItemEvent($trigger, $mapToSecretRoom->getId(), '*');
        $gameController->getEventController()->addEvent($event);

        // Give the player a reward for entering the secret room.
        $enteredSecretRoomReward = new Item(
            'enteredSecretRoomReward',
            'Reward for Entering Secret Room',
            'You did it! You made it into the secret room. This reward is proof of your achievement.',
            ['enter reward', 'reward.enter', 'reward']
        );

        $trigger = new AddItemToInventoryUseTrigger($enteredSecretRoomReward);
        $event = new EnterLocationEvent($trigger, 'secretRoom');
        $gameController->eventController->addEvent($event);

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