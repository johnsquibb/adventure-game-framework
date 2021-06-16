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
$locations = $builder->getLocations();

// Items
$flashlight = $items['flashlight'];
$secretLetter = $items['secretLetter'];
$enteredSecretRoomReward = $items['enteredSecretRoomReward'];
$mapToSecretRoom = $items['mapToSecretRoom'];

// Keys
$keyToWoodenDoor = $items['keyToWoodenDoor'];

// Containers
$chest = $containers['treasureChest'];

// Locations
$roomWestOfSpawn = $locations['roomWestOfSpawn'];
$hallwayLeadingSouth = $locations['hallwayLeadingSouthFromSpawn'];
$courtyard = $locations['courtyard'];
$houseInTown = $locations['houseInTown'];
$smallShed = $locations['smallShed'];
$cellar = $locations['cellar'];
$spawn = $locations['spawn'];
$secretRoom = $locations['secretRoom'];

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
$doorFromSecretRoomToRoomWestOfSpawn = $portals['doorFromSecretRoomToRoomWestOfSpawn'];

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
    $spawn,
    $roomWestOfSpawn,
    $hallwayLeadingSouth,
    $courtyard,
    $smallShed,
    $houseInTown,
    $cellar,
];
$platformManifest->setLocations($locations);
$platformManifest->setEvents($events);