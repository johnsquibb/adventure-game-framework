<?php

namespace AdventureGame\Command\Commands;

use AdventureGame\Command\CommandInterface;
use AdventureGame\Game\Exception\PlayerLocationNotSetException;
use AdventureGame\Game\GameController;
use AdventureGame\Item\ContainerEntityInterface;
use AdventureGame\Response\Message\ContainerMessage;
use AdventureGame\Response\Message\UnableMessage;
use AdventureGame\Response\Response;

/**
 * Class VerbPrepositionNounCommand processes verb+preposition+noun commands, e.g. "look at spoon".
 * @package AdventureGame\Command\Commands
 */
class VerbPrepositionNounCommand extends AbstractCommand implements CommandInterface
{
    public const PREPOSITION_AT = 'at';
    public const PREPOSITION_IN = 'in';

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
        if ($response = $this->tryLookInInventoryAction($gameController)) {
            return $response;
        }

        return $this->tryLookInLocationAction($gameController);
    }

    /**
     * Attempt to look at objects in player inventory.
     * @param GameController $gameController
     * @return Response|null
     */
    private function tryLookInInventoryAction(GameController $gameController): ?Response
    {
        switch ($this->verb) {
            case self::COMMAND_EXAMINE:
                switch ($this->preposition) {
                    case self::PREPOSITION_AT:
                        return $this->tryLookAtItemsByTagInPlayerInventory(
                            $gameController,
                            $this->noun
                        );
                }
        }

        return null;
    }

    /**
     * Attempt to look at objects in current player location.
     * @param GameController $gameController
     * @return Response|null
     * @throws PlayerLocationNotSetException
     */
    private function tryLookInLocationAction(GameController $gameController): ?Response
    {
        switch ($this->verb) {
            case self::COMMAND_EXAMINE:
                switch ($this->preposition) {
                    case self::PREPOSITION_AT:
                        return $this->tryLookAtItemsByTagAtPlayerLocationAction(
                            $gameController,
                            $this->noun
                        );
                    case self::PREPOSITION_IN:
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
            if ($container instanceof ContainerEntityInterface) {
                if (empty($container->revealItems())) {
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
