<?php

namespace AdventureGame\Test\Event;

use AdventureGame\Event\TriggerInterface;
use AdventureGame\Game\GameController;
use AdventureGame\Response\Response;
use AdventureGame\Test\FrameworkTest;

abstract class AbstractEventTest extends FrameworkTest
{
    protected function createMockTrigger(Response $mockResponse): TriggerInterface
    {
        return new class($mockResponse) implements TriggerInterface {
            public function __construct(private Response $response)
            {
            }

            public function execute(GameController $gameController): ?Response
            {
                return $this->response;
            }

            public function getId(): string
            {
                return 'mockId';
            }
        };
    }
}