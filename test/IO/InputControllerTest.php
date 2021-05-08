<?php

namespace AdventureGame\Test\IO;

use AdventureGame\IO\InputController;

class InputControllerTest extends InputOutputControllerTest
{
    public function setUp(): void
    {
        parent::setUp();
        $this->inputOutputController = new InputController();
    }
}
