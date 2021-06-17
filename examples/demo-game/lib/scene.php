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

// TODO, convert to AGML when available.
$activators = [$items['switch1'], $items['switch2'], $items['switch3']];

$comp1 = new ActivatedComparison(true);
$comp2 = new ActivatedComparison(false);
$comp3 = new ActivatedComparison(true);
$comparisons = [$comp1, $comp2, $comp3];

// Events & Triggers
$events = [];

// When the player activates the correct sequence of switches in house, unlock the cellar door.
$trigger = new ActivatorPortalLockTrigger(
    $activators,
    $comparisons,
    $portals['cellarDoorIn']
);

$events[] = new ActivateItemEvent($trigger, 'switch1', 'houseInTown');
$events[] = new DeactivateItemEvent($trigger, 'switch1', 'houseInTown');
$events[] = new ActivateItemEvent($trigger, 'switch2', 'houseInTown');
$events[] = new DeactivateItemEvent($trigger, 'switch2', 'houseInTown');
$events[] = new ActivateItemEvent($trigger, 'switch3', 'houseInTown');
$events[] = new DeactivateItemEvent($trigger, 'switch3', 'houseInTown');

// When the player turns the flashlight on in the cellar, reveal the map to the secret room.
$trigger = new AddItemToLocationUseTrigger($items['mapToSecretRoom']);
$events[] = new ActivateItemEvent($trigger, 'flashlight', 'cellar');

// Apply the same trigger on room entry when the flashlight is already activated.
$events[] = new HasActivatedItemEvent($trigger, 'flashlight', 'cellar');

// When reading the map, add the secret room as a new location.
$trigger = new AddLocationToMapUseTrigger($locations['secretRoom']);
$trigger->addEntrance('roomWestOfSpawn', $portals['doorFromWestRoomToSecretRoom']);
$events[] = new ActivateItemEvent($trigger, 'mapToSecretRoom', '*');

$trigger = new AddItemToInventoryUseTrigger($items['enteredSecretRoomReward']);
$events[] = new EnterLocationEvent($trigger, 'secretRoom');

// Apply configuration.
$platformManifest->setLocations($locations);
$platformManifest->setEvents($events);