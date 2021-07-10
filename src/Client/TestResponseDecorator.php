<?php

namespace AdventureGame\Client;

use AdventureGame\Response\Response;

class TestResponseDecorator
{
    public function __construct(private Response $response)
    {
    }
}
