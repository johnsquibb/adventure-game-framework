<?php

namespace AdventureGame\Command\Commands;

use AdventureGame\Command\CommandInterface;
use AdventureGame\Game\GameController;
use AdventureGame\IO\OutputController;

class VerbPrepositionNounCommand extends AbstractCommand implements CommandInterface
{
    public function __construct(
        private string $verb,
        private string $preposition,
        private string $noun,
        OutputController $outputController,
    ) {
        parent::__construct($outputController);
    }

    /**
     * Process verb+noun action.
     * @param GameController $gameController
     */
    public function process(GameController $gameController): void
    {
        //
    }
}