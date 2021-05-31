<?php

namespace AdventureGame\Response\Choice;

use AdventureGame\Command\Exception\LoadGameException;
use AdventureGame\Response\Choice;
use AdventureGame\Response\ChoiceInterface;

class LoadGameChoice implements ChoiceInterface
{
    public function getChoice(): Choice
    {
        return new Choice(
            'Loading a game will erase all progress. Are you sure?',
            ['yes', 'no'],
            function (array $p) {
                echo "\n";
                if ($p['answer'] === 'yes') {
                    echo 'loading game...';
                    echo "\n\n";
                    sleep(1);
                    // TODO don't throw exceptions, find a better way to decouple this logic.
                    throw new LoadGameException();
                } else {
                    echo 'The adventure continues...';
                    echo "\n\n";
                }
            },
        );
    }
}