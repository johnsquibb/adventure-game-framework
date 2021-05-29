<?php

namespace AdventureGame\Response\Choice;

use AdventureGame\Response\Choice;
use AdventureGame\Response\ChoiceInterface;

class QuitGameChoice implements ChoiceInterface
{
    public function getChoice(): Choice
    {
        return new Choice(
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
        );
    }
}