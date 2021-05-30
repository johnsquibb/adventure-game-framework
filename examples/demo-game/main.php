<?php declare(strict_types=1);

use AdventureGame\Client\ConsoleClientController;
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
$platformManifest->setPlayerSpawnLocationId('spawn');

// Run game.
$platformFactory = new PlatformFactory($platformManifest);
$platformController = new PlatformController($platformFactory);
$consoleController = new ConsoleClientController();
$platformController->run($consoleController);