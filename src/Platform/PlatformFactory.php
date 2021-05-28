<?php

namespace AdventureGame\Platform;

use AdventureGame\Character\Character;
use AdventureGame\Command\CommandController;
use AdventureGame\Command\CommandFactory;
use AdventureGame\Command\CommandParser;
use AdventureGame\Event\EventController;
use AdventureGame\Game\Exception\InvalidSaveDirectoryException;
use AdventureGame\Game\GameController;
use AdventureGame\Game\MapController;
use AdventureGame\Game\PlayerController;
use AdventureGame\IO\InputController;
use AdventureGame\IO\OutputController;
use AdventureGame\Item\Container;

/**
 * Class PlatformFactory provides factory and initialization for platform components.
 * @package AdventureGame\Platform
 */
class PlatformFactory
{
    private array $registry = [];

    public function __construct(private PlatformManifest $platformManifest)
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
        $object = $this->getRegisteredObject(CommandParser::class);
        if ($object === null) {
            $object = new CommandParser(
                $this->platformManifest->getVerbs(),
                $this->platformManifest->getNouns(),
                $this->platformManifest->getArticles(),
                $this->platformManifest->getPrepositions(),
                $this->platformManifest->getAliases(),
                $this->platformManifest->getShortcuts(),
                $this->platformManifest->getPhrases()
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

        $object->setSaveDirectory($this->platformManifest->getSaveGameDirectory());

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

        $locations = $this->platformManifest->getLocations();
        $object = new MapController($locations);
        $this->registerObject($object);

        $object->setPlayerLocationById($this->platformManifest->getPlayerSpawnLocationId());
        $events = $this->platformManifest->getEvents();
        $gameController = $this->getGameController();
        foreach ($events as $event) {
            $gameController->getEventController()->addEvent($event);
        }

        return $object;
    }

    /**
     * Get the player controller with the player character initialized.
     * @return PlayerController
     */
    private function getPlayerController(): PlayerController
    {
        $playerName = $this->platformManifest->getPlayerName();
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