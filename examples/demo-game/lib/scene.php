<?php declare(strict_types=1);

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
use AdventureGame\Item\ContainerItem;
use AdventureGame\Item\Item;
use AdventureGame\Location\Location;
use AdventureGame\Location\Portal;
use AdventureGameMarkupLanguage\Hydrator\ItemEntityHydrator;
use AdventureGameMarkupLanguage\Lexer;
use AdventureGameMarkupLanguage\Parser;
use AdventureGameMarkupLanguage\Transpiler;

global $platformManifest;

$lexer = new Lexer();
$parser = new Parser();
$transpiler = new Transpiler($lexer, $parser);

// Example of using Adventure Game Markup Language (AGML) to create an item.
$fixture = <<<END
        [ITEM]
        # Attributes
        id=flashlight
        size=2
        readable=yes
        name=Small Flashlight
        
        # Interactions
        acquirable=yes
        activatable=yes
        deactivatable=yes
        readable=yes
        
        # Tags 
        tags=flashlight,light,magic torch stick
        
        [description]
        A black metal flashlight that runs on rechargeable batteries.
        There is a round gray button for activating it.
        There is some small text printed on a label on the side of the flashlight.
        
        [text]
        Information written on the side:
        Model: Illuminated Devices Inc
        Year: 1983
        Serial Number: #8301IDI001256703
        Batt. Type: (4) AA
        END;

$hydrators = $transpiler->transpile($fixture);
$hydrator = $hydrators[0];
if ($hydrator instanceof ItemEntityHydrator) {
    $id = $hydrator->getId();
    $name = $hydrator->getName();
    $description = implode("\n    ", $hydrator->getDescription());
    $tags = $hydrator->getTags();

    $flashlight = new Item($id, $name, $description, $tags);
    $flashlight->setSize($hydrator->getSize());
    $flashlight->setActivatable($hydrator->getActivatable());
    $flashlight->setDeactivatable($hydrator->getDeactivatable());
    $flashlight->setReadable($hydrator->getReadable());
    $flashlight->setLines($hydrator->getText());
}

//-------------------------------
// Items
//-------------------------------

$secretLetter = new Item(
    'secretLetter',
    'A Secret Letter',
    'A folded letter written on old paper.',
    ['secret letter', 'letter']
);
$secretLetter->setSize(1);
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

// Give the player a reward for entering the secret room.
$enteredSecretRoomReward = new Item(
    'enteredSecretRoomReward',
    'Reward for Entering Secret Room',
    'You did it! You made it into the secret room. This reward is proof of your achievement.',
    ['enter reward', 'reward.enter', 'reward']
);

$mapToSecretRoom = new Item(
    'mapToSecretRoom',
    'Map to Secret Room',
    'A map detailing the location of a secret room. A speakable word is written on the map.',
    ['map']
);
$mapToSecretRoom->setActivatable(true);
$mapToSecretRoom->setSize(1);

//-------------------------------
// Keys
//-------------------------------
$keyToWoodenDoor = new Item(
    'keyToWoodenDoor',
    'Key to Wooden Door',
    'A metal key that unlocks the wooden door at spawn.',
    ['key to wooden door', 'key.keyToWoodenDoor', 'key']
);
$keyToWoodenDoor->setSize(1);

//-------------------------------
// Containers
//-------------------------------

$chest = new ContainerItem(
    'treasureChest',
    'Treasure Chest',
    'A chest containing valuable treasure.',
    ['chest'],
);
$chest->setCapacity(5);
$chest->setAcquirable(false);
$chest->addItem($flashlight);
$chest->addItem($keyToWoodenDoor);

//-------------------------------
// Portals
//-------------------------------

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
    'east',
    'spawn'
);

$doorFromWestRoomToSpawn->setKeyEntityId($keyToWoodenDoor->getId());

$doorFromWestRoomToSecretRoom = new Portal(
    'doorFromWestRoomToSecretRoom',
    'Secret Door',
    'A secret door has been revealed to the west.',
    ['door'],
    'west',
    'secretRoom'
);

$entryFromSpawnToHallway = new Portal(
    'entryFromSpawnToHallway',
    'Hallway Entrance',
    'An entrance to a hallway leading south.',
    ['hallway'],
    'south',
    'hallwayLeadingSouthFromSpawn'
);

$entryFromHallwayToSpawn = new Portal(
    'entryFromSpawnToHallway',
    'Hallway Entrance',
    'An entrance to a hallway leading north.',
    ['hallway'],
    'north',
    'spawn'
);

$doorFromHallwayToCourtyard = new Portal(
    'doorFromHallwayToCourtyard',
    'Front door',
    'A door with a window, through which you can see an exterior courtyard.',
    ['door'],
    'south', 'courtyard'
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

$pathFromTownToCourtyard = new Portal(
    'pathFromTownToCourtyard',
    'Path from Town',
    "A light walking path that leads north away from town.",
    ['path'],
    'north',
    'courtyard'
);

$cellarDoorIn = new Portal(
    'cellarDoorIn',
    'Door to Cellar',
    "A door leading down into a cellar.",
    ['door'],
    'down',
    'cellar'
);
$cellarDoorIn->setMutable(true);

// The cellar door will be unlocked by activating a switch in the House location.
$cellarDoorIn->setLocked(true);

$cellarDoorOut = new Portal(
    'cellarDoorOut',
    'Cellar Door',
    "The way out of the cellar.",
    ['door'],
    'up',
    'houseInTown'
);

$stepsFromShedToCourtyard = new Portal(
    'stepsFromShedToCourtyard',
    'Steps Leading Up',
    "Stone steps leading up to a courtyard.",
    ['steps'],
    'up',
    'courtyard'
);

$doorFromSecretRoomToWestRoom = new Portal(
    'doorFromWestRoomToSecretRoom',
    'Secret Door',
    'Exit to the east',
    ['door'],
    'east', 'roomWestOfSpawn'
);

//-------------------------------
// Locations
//-------------------------------

$roomWestOfSpawn = new Location(
    'roomWestOfSpawn',
    'Room West of Spawn',
    'There is nothing special about this room. It is just an ordinary room with walls.',
    new Container(),
    [$doorFromWestRoomToSpawn],
);
$roomWestOfSpawn->getContainer()->setCapacity(20);

$hallwayLeadingSouth = new Location(
    'hallwayLeadingSouthFromSpawn',
    'Hallway Leading South',
    'A hallway that leads south from spawn with a single exit to exterior courtyard',
    new Container(),
    [$doorFromHallwayToCourtyard, $entryFromHallwayToSpawn]
);
$hallwayLeadingSouth->getContainer()->setCapacity(5);

$courtyard = new Location(
    'courtyard',
    'Courtyard',
    'A courtyard surrounds the entrance of the house. ' . "\n" .
    'Hedges form a wall in three directions, with a path leading away from the house toward town.',
    new Container(),
    [$doorFromCourtyardToHallway, $pathFromCourtyardToTown, $stepsFromCourtyardToShed]
);
$courtyard->getContainer()->setCapacity(40);

$houseInTown = new Location(
    "houseInTown",
    "The House",
    "A house belonging to someone. They don't appear to be home.",
    new Container(),
    [$pathFromTownToCourtyard, $cellarDoorIn]
);
$houseInTown->getContainer()->setCapacity(20);

$smallShed = new Location(
    "smallShed",
    "A small shed",
    "A small shed with weathered siding and a small window.",
    new Container(),
    [$stepsFromShedToCourtyard]
);
$smallShed->getContainer()->setCapacity(10);

$cellar = new Location(
    "cellar",
    "Cellar",
    "A dark cellar with a low ceiling. It is difficult to see anything without some kind of light.",
    new Container(),
    [$cellarDoorOut]
);
$cellar->getContainer()->setCapacity(20);

$spawnRoom = new Location(
    'spawn',
    'Player Spawn',
    'This is the starting room.',
    new Container(),
    [$doorFromSpawnToWestRoom, $entryFromSpawnToHallway],
);
$spawnRoom->getContainer()->setCapacity(20);
$spawnRoom->getContainer()->addItem($chest);

$secretRoom = new Location(
    'secretRoom',
    'The Secret Room',
    'You have discovered a secret room.',
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
    "There's no telling what this switch does.",
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