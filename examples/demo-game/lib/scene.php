<?php declare(strict_types=1);

use AdventureGame\Builder\SceneBuilder;
use AdventureGame\Event\Events\ActivateItemEvent;
use AdventureGame\Event\Events\DeactivateItemEvent;
use AdventureGame\Event\Events\EnterLocationEvent;
use AdventureGame\Event\Events\HasActivatedItemEvent;
use AdventureGame\Event\Triggers\AddItemToInventoryUseTrigger;
use AdventureGame\Event\Triggers\AddLocationToMapUseTrigger;
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
$triggers = $builder->getTriggers();

// TODO, convert to AGML when available.
// Events
$events = [];

// When the player activates the correct sequence of switches in house, unlock the cellar door.
$events[] = new ActivateItemEvent($triggers['activateSwitchesInHouse'], 'switch1', 'houseInTown');
$events[] = new DeactivateItemEvent($triggers['activateSwitchesInHouse'], 'switch1', 'houseInTown');
$events[] = new ActivateItemEvent($triggers['activateSwitchesInHouse'], 'switch2', 'houseInTown');
$events[] = new DeactivateItemEvent($triggers['activateSwitchesInHouse'], 'switch2', 'houseInTown');
$events[] = new ActivateItemEvent($triggers['activateSwitchesInHouse'], 'switch3', 'houseInTown');
$events[] = new DeactivateItemEvent($triggers['activateSwitchesInHouse'], 'switch3', 'houseInTown');

// When the player turns the flashlight on in the cellar, reveal the map to the secret room.
$events[] = new ActivateItemEvent(
    $triggers['flashlightActivatedInCellarToRevealMap'],
    'flashlight',
    'cellar'
);

// Apply the same trigger on room entry when the flashlight is already activated.
$events[] = new HasActivatedItemEvent(
    $triggers['flashlightActivatedInCellarToRevealMap'],
    'flashlight',
    'cellar'
);

// When reading the map, add the secret room as a new location.
$events[] = new ActivateItemEvent($triggers['addSecretRoomToMap'], 'mapToSecretRoom', '*');

//$trigger = new AddItemToInventoryUseTrigger($items['enteredSecretRoomReward']);
$events[] = new EnterLocationEvent($triggers['giveSecretRoomReward'], 'secretRoom');

// Apply configuration.
$platformManifest->setLocations($locations);
$platformManifest->setEvents($events);