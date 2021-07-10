<?php

declare(strict_types=1);

use AdventureGame\Client\Test\InventoryTest;
use AdventureGame\Client\Test\LocationTest;
use AdventureGame\Client\TestClientController;
use AdventureGame\Client\TestErrorException;
use AdventureGame\Client\TestsCompleteException;
use AdventureGame\Platform\PlatformController;
use AdventureGame\Platform\PlatformFactory;
use AdventureGame\Platform\PlatformManifest;

$directory = __DIR__;
$projectDirectory = dirname(dirname($directory));
$configurationDirectory = $directory . '/config';
$libDirectory = $directory . '/lib';
$saveGameDirectory = $directory . '/data/saves';

require $projectDirectory . '/vendor/autoload.php';

$platformManifest = new PlatformManifest();
$platformManifest->setSaveGameDirectory($saveGameDirectory);

// Apply game vocabulary.
require $libDirectory . '/vocabulary.php';

// Build game scene.
require $libDirectory . '/scene.php';

$platformManifest->setPlayerName('test-player');
$platformManifest->setPlayerInventoryCapacity(4);
$platformManifest->setPlayerSpawnLocationId('spawn');

// Play the game robot.
$platformFactory = new PlatformFactory($platformManifest);
$platformController = new PlatformController($platformFactory);

$tests = [
    new LocationTest('go south', 'hallwayLeadingSouthFromSpawn'),
    new LocationTest('go north', 'spawn'),
    new InventoryTest('open chest', []),
    new InventoryTest('take key from chest', ['keyToWoodenDoor']),
    new InventoryTest('take flashlight from chest', ['keyToWoodenDoor', 'flashlight']),
    new LocationTest('go south', 'hallwayLeadingSouthFromSpawn'),
    new LocationTest('go south', 'courtyard'),
    new LocationTest('go down steps', 'smallShed'),
    new LocationTest('go up steps', 'courtyard'),
    new LocationTest('take path to town', 'houseInTown'),
    new LocationTest('activate first switch', 'houseInTown'),
    new LocationTest('activate third switch', 'houseInTown'),
    new LocationTest('go down', 'cellar'),
    new LocationTest('turn flashlight on', 'cellar'),
    new InventoryTest('take map', ['keyToWoodenDoor', 'flashlight', 'mapToSecretRoom']),
    new InventoryTest('drop flashlight', ['keyToWoodenDoor', 'mapToSecretRoom']),
    new LocationTest('go up', 'houseInTown'),
    new LocationTest('take path away from town', 'courtyard'),
    new LocationTest('go through front door', 'hallwayLeadingSouthFromSpawn'),
    new LocationTest('enter hallway', 'spawn'),
    new LocationTest('unlock wooden door with key to wooden door', 'spawn'),
    new LocationTest('go west', 'roomWestOfSpawn'),
    new LocationTest('read map', 'roomWestOfSpawn'),
    new LocationTest('go west', 'secretRoom'),
    new InventoryTest(
        'inventory',
        ['keyToWoodenDoor', 'mapToSecretRoom', 'enteredSecretRoomReward']
    ),
    new InventoryTest('drop everything', []),
    new InventoryTest('take letter', ['secretLetter']),
    new InventoryTest('read letter', ['secretLetter']),
];

$testClient = new TestClientController(
    $platformController->getPlatformRegistry(),
    $tests
);

if (isset($argv[1])) {
    $testClient->setWaitTimeMilliseconds((int)$argv[1]);
}

try {
    $platformController->run($testClient);
} catch (TestsCompleteException $e) {
    echo "\n\n", $e->getMessage(), "\n\n";
    exit;
} catch (TestErrorException $e) {
    echo "\nTest Error!\n\n";
    exit;
}
