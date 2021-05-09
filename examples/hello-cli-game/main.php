<?php declare(strict_types=1);

use AdventureGame\Client\ConsoleClientController;
use AdventureGame\Platform\PlatformController;
use AdventureGame\Platform\PlatformFactory;

require_once dirname(dirname(__DIR__)) . '/vendor/autoload.php';

// Try: `take test-item-in-container from test-container-item`

$platformFactory = new PlatformFactory();
$platformRegistry = $platformFactory->createPlatformRegistry();

$platformController = new PlatformController($platformRegistry);
$platformController->run(new ConsoleClientController());