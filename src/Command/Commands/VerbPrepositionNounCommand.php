<?php

namespace AdventureGame\Command\Commands;

use AdventureGame\Command\CommandInterface;
use AdventureGame\Game\Exception\PlayerLocationNotSetException;
use AdventureGame\Game\GameController;
use AdventureGame\Item\ContainerInterface;
use AdventureGame\Response\Message\ContainerMessage;
use AdventureGame\Response\Message\UnableMessage;
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
                    case 'in':
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

        if (empty($items)) {
            $message = new UnableMessage($tag, UnableMessage::TYPE_ITEM_NOT_FOUND);
            $response->addMessage($message->toString());
            return $response;
        }

        foreach ($items as $container) {
            if ($container instanceof ContainerInterface) {
                if (empty($container->getItems())) {
                    $message = new ContainerMessage($tag, ContainerMessage::TYPE_CONTAINER_EMPTY);
                    $response->addMessage($message->toString());
                }

                foreach ($this->listContainerItems($container) as $description) {
                    $response->addContainerDescription($description);
                }
            }
        }

        return $response;
    }
}