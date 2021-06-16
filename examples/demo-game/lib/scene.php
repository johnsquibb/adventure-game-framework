<?php declare(strict_types=1);

use AdventureGame\Builder\SceneBuilder;
use AdventureGame\Event\Events\ActivateItemEvent;
use AdventureGame\Event\Events\DeactivateItemEvent;
use AdventureGame\Event\Events\EnterLocationEvent;
use AdventureGame\Event\Events\HasActivatedItemEvent;
use AdventureGame\Event\Triggers\ActivatorPortalLockTrigger;
use AdventureGame\Event\Triggers\AddItemToInventoryUseTrigger;
use AdventureGame\Event\Triggers\AddItemToLocationUseTrigger;
use AdventureGame\Event\Triggers\AddLocationToMapUseTrigger;
use AdventureGame\Event\Triggers\Comparisons\ActivatedComparison;
use AdventureGame\Item\Container;
use AdventureGame\Item\Item;
use AdventureGame\Location\Location;
use AdventureGameMarkupLanguage\Lexer;
use AdventureGameMarkupLanguage\Parser;
use AdventureGameMarkupLanguage\Transpiler;

global $platformManifest;

// Example of using Adventure Game Markup Language (AGML) to create an item.
global $libDirectory;
$markup = file_get_contents($libDirectory . '/markup/scene.agml');

$lexer = new Lexer();
$parser = new Parser();
$transpiler = new Transpiler($lexer, $parser);
$builder = new SceneBuilder($transpiler);

$builder->transpileMarkup($markup);
$items = $builder->getItems();
$containers = $builder->getContainers();
$portals = $builder->getPortals();

// Items
$flashlight = $items['flashlight'];
$secretLetter = $items['secretLetter'];
$enteredSecretRoomReward = $items['enteredSecretRoomReward'];
$mapToSecretRoom = $items['mapToSecretRoom'];

// Keys
$keyToWoodenDoor = $items['keyToWoodenDoor'];

// Containers
$chest = $containers['treasureChest'];

// Location Exits
$doorFromSpawnToWestRoom = $portals['doorFromSpawnToWestRoom'];
$doorFromWestRoomToSpawn = $portals['doorFromWestRoomToSpawn'];
$doorFromWestRoomToSecretRoom = $portals['doorFromWestRoomToSecretRoom'];
$entryFromSpawnToHallway = $portals['entryFromSpawnToHallway'];
$entryFromHallwayToSpawn = $portals['entryFromHallwayToSpawn'];
$doorFromHallwayToCourtyard = $portals['doorFromHallwayToCourtyard'];
$doorFromCourtyardToHallway = $portals['doorFromCourtyardToHallway'];
$pathFromCourtyardToTown = $portals['pathFromCourtyardToTown'];
$stepsFromCourtyardToShed = $portals['stepsFromCourtyardToShed'];
$pathFromTownToCourtyard = $portals['pathFromTownToCourtyard'];
$cellarDoorIn = $portals['cellarDoorIn'];
$cellarDoorOut = $portals['cellarDoorOut'];
$stepsFromShedToCourtyard = $portals['stepsFromShedToCourtyard'];
$doorFromSecretRoomToWestRoom = $portals['doorFromWestRoomToSecretRoom'];

//-------------------------------
// Locations
//-------------------------------

$roomWestOfSpawn = new Location(
    'roomWestOfSpawn',
    'Room West of Spawn',
    ['There is nothing special about this room. It is just an ordinary room with walls.'],
    new Container(),
    [$doorFromWestRoomToSpawn],
);
$roomWestOfSpawn->getContainer()->setCapacity(20);

$hallwayLeadingSouth = new Location(
    'hallwayLeadingSouthFromSpawn',
    'Hallway Leading South',
    ['A hallway that leads south from spawn with a single exit to exterior courtyard'],
    new Container(),
    [$doorFromHallwayToCourtyard, $entryFromHallwayToSpawn]
);
$hallwayLeadingSouth->getContainer()->setCapacity(5);

$courtyard = new Location(
    'courtyard',
    'Courtyard',
    [
        'A courtyard surrounds the entrance of the house.',
        'Hedges form a wall in three directions, with a path leading away from the house toward town.'
    ],
    new Container(),
    [$doorFromCourtyardToHallway, $pathFromCourtyardToTown, $stepsFromCourtyardToShed]
);
$courtyard->getContainer()->setCapacity(40);

$houseInTown = new Location(
    "houseInTown",
    "The House",
    ["A house belonging to someone. They don't appear to be home."],
    new Container(),
    [$pathFromTownToCourtyard, $cellarDoorIn]
);
$houseInTown->getContainer()->setCapacity(20);

$smallShed = new Location(
    "smallShed",
    "A small shed",
    ["A small shed with weathered siding and a small window."],
    new Container(),
    [$stepsFromShedToCourtyard]
);
$smallShed->getContainer()->setCapacity(10);

$cellar = new Location(
    "cellar",
    "Cellar",
    ["A dark cellar with a low ceiling. It is difficult to see anything without some kind of light."],
    new Container(),
    [$cellarDoorOut]
);
$cellar->getContainer()->setCapacity(20);

$spawnRoom = new Location(
    'spawn',
    'Player Spawn',
    ['This is the starting room.'],
    new Container(),
    [$doorFromSpawnToWestRoom, $entryFromSpawnToHallway],
);
$spawnRoom->getContainer()->setCapacity(20);
$spawnRoom->getContainer()->addItem($chest);

$secretRoom = new Location(
    'secretRoom',
    'The Secret Room',
    ['You have discovered a secret room.'],
    new Container(),
    [$doorFromSecretRoomToWestRoom],
);
$secretRoom->getContainer()->setCapacity(20);
$secretRoom->getContainer()->addItem($secretLetter);

// ---------------------
// Switches & Activators
// ---------------------

$switch1 = new Item(
    'switch1',
    'Switch 1',
    ["There's no telling what this switch does."],
    ['switch.one']
);
$switch1->setActivatable(true);
$switch1->setDeactivatable(true);
$switch1->setAcquirable(false);
$houseInTown->getContainer()->addItem($switch1);
$houseInTown->getContainer()->setCapacity(20);

$switch2 = new Item(
    'switch2',
    'Switch 2',
    ["There's no telling what this switch does."],
    ['switch.two']
);
$switch2->setActivatable(true);
$switch2->setDeactivatable(true);
$switch2->setAcquirable(false);
$houseInTown->getContainer()->addItem($switch2);

$switch3 = new Item(
    'switch3',
    'Switch 3',
    ["There's no telling what this switch does."],
    ['switch.three']
);
$switch3->setActivatable(true);
$switch3->setDeactivatable(true);
$switch3->setAcquirable(false);
$houseInTown->getContainer()->addItem($switch3);

$activators = [$switch1, $switch2, $switch3];

$comp1 = new ActivatedComparison(true);
$comp2 = new ActivatedComparison(false);
$comp3 = new ActivatedComparison(true);

$comparisons = [$comp1, $comp2, $comp3];

// ------------------
// Events & Triggers
// ------------------
$events = [];

// When the player activates the correct sequence of switches in house, unlock the cellar door.
$trigger = new ActivatorPortalLockTrigger(
    $activators,
    $comparisons,
    $cellarDoorIn
);

$events[] = new ActivateItemEvent($trigger, $switch1->getId(), $houseInTown->getId());
$events[] = new DeactivateItemEvent($trigger, $switch1->getId(), $houseInTown->getId());
$events[] = new ActivateItemEvent($trigger, $switch2->getId(), $houseInTown->getId());
$events[] = new DeactivateItemEvent($trigger, $switch2->getId(), $houseInTown->getId());
$events[] = new ActivateItemEvent($trigger, $switch3->getId(), $houseInTown->getId());
$events[] = new DeactivateItemEvent($trigger, $switch3->getId(), $houseInTown->getId());

// When the player turns the flashlight on in the cellar, reveal the map to the secret room.
$trigger = new AddItemToLocationUseTrigger($mapToSecretRoom);
$events[] = new ActivateItemEvent($trigger, 'flashlight', 'cellar');

// Apply the same trigger on room entry when the flashlight is already activated.
$events[] = new HasActivatedItemEvent($trigger, 'flashlight', 'cellar');

// When reading the map, add the secret room as a new location.
$trigger = new AddLocationToMapUseTrigger($secretRoom);
$trigger->addEntrance('roomWestOfSpawn', $doorFromWestRoomToSecretRoom);
$events[] = new ActivateItemEvent($trigger, $mapToSecretRoom->getId(), '*');

$trigger = new AddItemToInventoryUseTrigger($enteredSecretRoomReward);
$events[] = new EnterLocationEvent($trigger, 'secretRoom');

// Apply configuration.
$locations = [
    $spawnRoom,
    $roomWestOfSpawn,
    $hallwayLeadingSouth,
    $courtyard,
    $smallShed,
    $houseInTown,
    $cellar,
];
$platformManifest->setLocations($locations);
$platformManifest->setEvents($events);