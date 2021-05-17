<?php

namespace AdventureGame\Test\Response;

use AdventureGame\Response\Description;
use AdventureGame\Response\Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    public function testAddContainerDescription()
    {
        $response = new Response();
        $description = new Description();

        $response->addContainerDescription($description);
        $this->assertEquals($response->getContainers(), [$description]);

        $response->addContainerDescription($description);
        $this->assertEquals($response->getContainers(), [$description, $description]);
    }

    public function testAddExitDescription()
    {
        $response = new Response();
        $description = new Description();

        $response->addExitDescription($description);
        $this->assertEquals($response->getExits(), [$description]);

        $response->addExitDescription($description);
        $this->assertEquals($response->getExits(), [$description, $description]);
    }

    public function testAddItemDescription()
    {
        $response = new Response();
        $description = new Description();

        $response->addItemDescription($description);
        $this->assertEquals($response->getItems(), [$description]);

        $response->addItemDescription($description);
        $this->assertEquals($response->getItems(), [$description, $description]);
    }
}
