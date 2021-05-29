<?php

namespace AdventureGame\Response\Choice;

use AdventureGame\Command\Exception\StartNewGameException;
use AdventureGame\Response\Choice;
use AdventureGame\Response\ChoiceInterface;

class NewGameChoice implements ChoiceInterface
{
    public function getChoice(): Choice
    {
        return new Choice(
            'Starting a new game will erase all progress. Are you sure?',
            ['yes', 'no'],
            function (array $p) {
                echo "\n";
                if ($p['answer'] === 'yes') {
                    echo 'starting new game...';
                    echo "\n\n";
                    sleep(1);
                    // TODO don't throw exceptions, find a better way to decouple this logic.
                    throw new StartNewGameException();
                } else {
                    echo 'The adventure continues...';
                    echo "\n\n";
                }
            },
        );
    }
}