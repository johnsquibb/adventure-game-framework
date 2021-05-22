<?php

namespace AdventureGame\Command\Commands;

use AdventureGame\Command\CommandInterface;
use AdventureGame\Game\Exception\PlayerLocationNotSetException;
use AdventureGame\Game\GameController;
use AdventureGame\Item\ContainerInterface;
use AdventureGame\Response\Response;

/**
 * Class VerbPrepositionNounCommand processes verb+preposition+noun commands, e.g. "look at spoon".
 * @package AdventureGame\Command\Commands
 */
class VerbPrepositionNounCommand extends AbstractCommand implements CommandInterface
{
    public function __construct(
        private string $verb,
        private string $preposition,
        private string $noun
    ) {
    }

    /**
     * Process verb+noun action.
     * @param GameController $gameController
     * @return Response|null
     * @throws PlayerLocationNotSetException
     */
    public function process(GameController $gameController): ?Response
    {
        return $this->tryLookAction($gameController);
    }

    /**
     * Attempt to look at objects.
     * @param GameController $gameController
     * @return Response|null
     * @throws PlayerLocationNotSetException
     */
    private function tryLookAction(GameController $gameController): ?Response
    {
        switch ($this->verb) {
            case 'look':
                switch ($this->preposition) {
                    case 'at':
                        return $this->tryLookAtItemsByTagAtPlayerLocationAction(
                            $gameController,
                            $this->noun
                        );
                    case 'inside':
                        return $this->tryLookInsideContainersByTagAtPlayerLocationAction(
                            $gameController,
                            $this->noun
                        );
                }
        }

        return null;
    }

    /**
     * Try to look inside the first container matching tag in the current player location.
     * @param GameController $gameController
     * @param string $tag
     * @return Response
     * @throws PlayerLocationNotSetException
     */
    private function tryLookInsideContainersByTagAtPlayerLocationAction(
        GameController $gameController,
        string $tag
    ): Response {
        $response = new Response();

        $items = $gameController->mapController
            ->getPlayerLocation()->getContainer()->getItemsByTag($tag);

        if (count($items)) {
            foreach ($items as $container) {
                if ($container instanceof ContainerInterface) {
                    foreach ($this->listContainerItems($container) as $description) {
                        $response->addContainerDescription($description);
                    }
                }
            }
        }

        return $response;
    }
}