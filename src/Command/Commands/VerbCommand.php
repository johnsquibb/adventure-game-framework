<?php

namespace AdventureGame\Command\Commands;

use AdventureGame\Command\CommandInterface;
use AdventureGame\Command\Exception\StartNewGameException;
use AdventureGame\Game\Exception\InvalidSaveDirectoryException;
use AdventureGame\Game\Exception\PlayerLocationNotSetException;
use AdventureGame\Game\GameController;
use AdventureGame\Response\Response;
use AdventureGame\Response\Trigger;

/**
 * Class VerbCommand processes single-word verb commands, e.g. "take" or "eat".
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
        $response = $this->tryLookAction($gameController);
        if ($response) {
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
            case 'look':
                return $this->describePlayerLocation($gameController);
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
        // TODO: see if we can implement a callback system to prompt user for additional input.
        // The client can then show its desired interface, collect the input, then trigger the callback
        // function to do the thing.
        switch ($this->verb) {
            case "save":
                return $this->saveGame($gameController);
            case 'load':
                return $this->loadGame($gameController);
            case 'new':
                return $this->newGame($gameController);
            case 'quit':
                return $this->quitGame($gameController);
        }

        return null;
    }

    /**
     * Save the game.
     * @param GameController $gameController
     * @return Response
     * @throws InvalidSaveDirectoryException
     */
    private function saveGame(GameController $gameController): Response
    {
        $response = new Response();

        $serialized = serialize($gameController);
        $file = $gameController->getSaveDirectory();
        $file .= '/save.txt';
        file_put_contents($file, $serialized);

        $response->addMessage('Game saved');

        return $response;
    }

    /**
     * Load a game.
     * @param GameController $gameController
     * @return Response
     * @throws InvalidSaveDirectoryException
     */
    private function loadGame(GameController $gameController): Response
    {
        $response = new Response();

        $file = $gameController->getSaveDirectory();
        $file .= '/save.txt';
        if (!file_exists($file)) {
            $response->addMessage('Could not find save file.');
            return $response;
        }

        $serialized = file_get_contents($file);
        $object = @unserialize($serialized);
        if ($object === false) {
            $response->addMessage('Save file could not be recovered, it may be corrupted.');
            return $response;
        }

        if ($object instanceof GameController) {
            $gameController->hydrateFromSave($object);
            $response->addMessage('Game loaded.');
        } else {
            $response->addMessage('Could not load saved game.');
        }

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
        $response->addMessage('New game started');

        $response->setTrigger(
            new Trigger(
                'Starting a new game will erase all progress. Are you sure?',
                ['yes', 'no'],
                function (array $p) {
                    echo "\n";
                    if ($p['answer'] === 'yes') {
                        echo 'starting new game...';
                        echo "\n\n";
                        sleep(1);
                        throw new StartNewGameException();
                    } else {
                        echo 'The adventure continues...';
                        echo "\n\n";
                    }
                },
            )
        );

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

        $response->setTrigger(
            new Trigger(
                'Are you sure you want to quit?',
                ['yes', 'no'],
                function (array $p) {
                    echo "\n";
                    if ($p['answer'] === 'yes') {
                        echo 'exiting...';
                        echo "\n\n";
                        exit;
                    } else {
                        echo 'The adventure continues...';
                        echo "\n\n";
                    }
                },
            )
        );

        return $response;
    }
}