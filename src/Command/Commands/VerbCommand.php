<?php

namespace AdventureGame\Command\Commands;

use AdventureGame\Command\CommandInterface;
use AdventureGame\Game\Exception\InvalidSaveDirectoryException;
use AdventureGame\Game\Exception\PlayerLocationNotSetException;
use AdventureGame\Game\GameController;
use AdventureGame\Response\Choice\LoadGameChoice;
use AdventureGame\Response\Choice\NewGameChoice;
use AdventureGame\Response\Choice\QuitGameChoice;
use AdventureGame\Response\Message\GameManagementMessage;
use AdventureGame\Response\Response;

/**
 * Class VerbCommand processes single-word verb commands, e.g. "examine" or "inventory".
 * It also handles game management commands such as 'save' or 'quit'.
 * @package AdventureGame\Command\Commands
 */
class VerbCommand extends AbstractCommand implements CommandInterface
{
    public function __construct(
        private string $verb
    ) {
    }

    /**
     * Process verb action.
     * @param GameController $gameController
     * @return Response|null
     * @throws PlayerLocationNotSetException|InvalidSaveDirectoryException
     */
    public function process(GameController $gameController): ?Response
    {
        if ($response = $this->tryLookAction($gameController)) {
            return $response;
        }

        if ($response = $this->tryInventoryAction($gameController)) {
            return $response;
        }

        return $this->tryGameAction($gameController);
    }

    /**
     * Look at current player area.
     * @param GameController $gameController
     * @return Response|null
     * @throws PlayerLocationNotSetException
     */
    private function tryLookAction(GameController $gameController): ?Response
    {
        switch ($this->verb) {
            case self::COMMAND_EXAMINE:
                return $this->describePlayerLocation($gameController);
        }

        return null;
    }

    /**
     * Inventory.
     * @param GameController $gameController
     * @return Response|null
     */
    private function tryInventoryAction(GameController $gameController): ?Response
    {
        switch ($this->verb) {
            case self::COMMAND_INVENTORY:
                return $this->describePlayerInventory($gameController);
        }

        return null;
    }

    /**
     * Manage game state.
     * @param GameController $gameController
     * @return Response|null
     * @throws InvalidSaveDirectoryException
     */
    private function tryGameAction(GameController $gameController): ?Response
    {
        switch ($this->verb) {
            case self::COMMAND_SAVE:
                return $this->saveGame($gameController);
            case self::COMMAND_LOAD:
                return $this->loadGame($gameController);
            case self::COMMAND_NEW:
                return $this->newGame($gameController);
            case self::COMMAND_QUIT:
                return $this->quitGame($gameController);
        }

        return null;
    }

    /**
     * Save the game.
     * @param GameController $gameController
     * @return Response
     * @throws InvalidSaveDirectoryException
     * @todo Implement choices for selecting which game to save using named 'slots'
     */
    private function saveGame(GameController $gameController): Response
    {
        $response = new Response();

        $serialized = serialize($gameController);
        $file = $gameController->getSaveDirectory();
        $file .= '/save.txt';
        file_put_contents($file, $serialized);

        $message = new GameManagementMessage(GameManagementMessage::TYPE_GAME_SAVED);
        $response->addMessage($message->toString());

        return $response;
    }

    /**
     * Load a game.
     * @param GameController $gameController
     * @return Response
     * @todo Implement choices for selecting which game to load using named 'slots'
     */
    private function loadGame(GameController $gameController): Response
    {
        $response = new Response();

        $loadGameChoice = new LoadGameChoice();
        $response->setChoice($loadGameChoice->getChoice());

        return $response;
    }

    /**
     * Start a new game.
     * @param GameController $gameController
     * @return Response
     */
    private function newGame(GameController $gameController): Response
    {
        $response = new Response();

        $newGameChoice = new NewGameChoice();
        $response->setChoice($newGameChoice->getChoice());

        return $response;
    }

    /**
     * Quit a game.
     * @param GameController $gameController
     * @return Response
     */
    private function quitGame(GameController $gameController): Response
    {
        $response = new Response();

        $newGameChoice = new QuitGameChoice();
        $response->setChoice($newGameChoice->getChoice());

        return $response;
    }
}