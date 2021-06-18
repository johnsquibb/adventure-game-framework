<?php

namespace AdventureGame\Platform;

use AdventureGame\Client\ClientControllerInterface;
use AdventureGame\Command\Exception\InvalidCommandException;
use AdventureGame\Command\Exception\InvalidTokenException;
use AdventureGame\Command\Exception\InvalidTokensLengthException;
use AdventureGame\Command\Exception\LoadGameException;
use AdventureGame\Command\Exception\StartNewGameException;
use AdventureGame\Game\Exception\InvalidExitException;
use AdventureGame\Game\Exception\InvalidSaveDirectoryException;
use AdventureGame\Game\GameController;
use AdventureGame\Response\Message\GameManagementMessage;
use AdventureGame\Response\Response;

/**
 * Class PlatformController processes client input into game instructions and handles the execution
 * of the game.
 * @package AdventureGame\Platform
 */
class PlatformController
{
    private PlatformRegistry $platformRegistry;

    public function __construct(
        private PlatformFactory $platformFactory
    ) {
        $this->platformRegistry = $this->platformFactory->createPlatformRegistry();
    }

    public function getPlatformRegistry(): PlatformRegistry
    {
        return $this->platformRegistry;
    }

    /**
     * Run the game.
     * @param ClientControllerInterface $clientController
     * @throws InvalidTokensLengthException|InvalidSaveDirectoryException
     */
    public function run(ClientControllerInterface $clientController): void
    {
        try {
            $this->runGameLoop($clientController);
        } catch (StartNewGameException) {
            // Rebuild initial game state and re-run.
            $this->platformFactory->clearRegistry();
            $this->platformRegistry = $this->platformFactory->createPlatformRegistry();
            $this->run($clientController);
        } catch (LoadGameException) {
            $gameController = $this->platformRegistry->gameController;
            $response = $this->loadGame($gameController);
            $clientController->processResponse($response);
            $this->run($clientController);
        }
    }

    /**
     * Enter the main game loop. This will run until an exit condition is reached.
     * @param ClientControllerInterface $clientController
     */
    private function runGameLoop(ClientControllerInterface $clientController): void
    {
        // On game load, show the current location.
        $response = $this->processInput('look');
        $clientController->processResponse($response);

        for (; ;) {
            $input = $clientController->getInput();
            if (!empty($input)) {
                $response = $this->processInput($input);
                $clientController->processResponse($response);
            }
        }
    }

    /**
     * Process user input.
     * @param string $input
     * @return Response
     */
    private function processInput(string $input): Response
    {
        try {
            $response = $this->platformRegistry->inputController->processInput($input);

            if ($response === null) {
                return $this->noCommandProcessedMessage();
            }

            return $response;
        } catch (InvalidCommandException | InvalidTokenException | InvalidTokensLengthException $e) {
            return $this->invalidCommandMessage();
        } catch (InvalidExitException $e) {
            return $this->invalidExitMessage();
        }
    }

    /**
     * Report no command processed to user.
     * @return Response
     */
    private function noCommandProcessedMessage(): Response
    {
        $response = new Response();

        $response->addMessage("Be more specific.");
        return $response;
    }

    /**
     * Report invalid command to user.
     * @return Response
     */
    private function invalidCommandMessage(): Response
    {
        $response = new Response();

        $response->addMessage("That can't be done here.");
        return $response;
    }

    /**
     * Report invalid exit choice to user.
     * @return Response
     */
    private function invalidExitMessage(): Response
    {
        $response = new Response();

        $response->addMessage("There is nothing in that direction.");
        return $response;
    }

    private function loadGame(GameController $gameController): Response
    {
        $response = new Response();

        $file = $gameController->getSaveDirectory();
        $file .= '/save.txt';
        if (!file_exists($file)) {
            $message = new GameManagementMessage(GameManagementMessage::TYPE_CANNOT_FIND_SAVE_FILE);
            $response->addMessage($message->toString());
            return $response;
        }

        $serialized = file_get_contents($file);
        $object = @unserialize($serialized);
        if ($object === false) {
            $message = new GameManagementMessage(GameManagementMessage::TYPE_UNSERIALIZE_ERROR);
            $response->addMessage($message->toString());
            return $response;
        }

        if ($object instanceof GameController) {
            $gameController->hydrateFromSave($object);
            $message = new GameManagementMessage(GameManagementMessage::TYPE_GAME_LOADED);
            $response->addMessage($message->toString());
        } else {
            $gameController->hydrateFromSave($object);
            $message = new GameManagementMessage(GameManagementMessage::TYPE_GAME_NOT_LOADED);
            $response->addMessage($message->toString());
        }

        return $response;
    }
}