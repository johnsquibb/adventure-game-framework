<?php

namespace AdventureGame\Platform;

use AdventureGame\Command\CommandController;
use AdventureGame\Game\GameController;
use AdventureGame\Game\MapController;
use AdventureGame\Game\PlayerController;
use AdventureGame\IO\InputController;
use AdventureGame\IO\OutputController;

/**
 * Class PlatformRegistry maintains references to various controllers and utilities used throughout
 * the framework.
 * @package AdventureGame\Platform
 */
final class PlatformRegistry
{
    public InputController $inputController;
    public OutputController $outputController;
    public CommandController $commandController;
    public GameController $gameController;
    public MapController $mapController;
    public PlayerController $playerController;
}
