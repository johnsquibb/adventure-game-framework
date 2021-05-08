<?php

namespace AdventureGame\Test\IO;

use AdventureGame\IO\OutputController;

class OutputControllerTest extends InputOutputControllerTest
{
    public function setUp(): void
    {
        parent::setUp();
        $this->inputOutputController = new OutputController();
    }
}
