<?php

namespace AdventureGame\Test\Response;

use AdventureGame\Response\Response;
use AdventureGame\Response\ResponseController;
use PHPUnit\Framework\TestCase;

class ResponseControllerTest extends TestCase
{
    public function testGetResponse()
    {
        $response = new Response();
        $responseController = new ResponseController($response);
        $this->assertEquals($response, $responseController->getResponse());
    }
}
