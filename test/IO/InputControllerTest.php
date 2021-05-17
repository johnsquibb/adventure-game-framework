<?php

namespace AdventureGame\Test\IO;

class InputControllerTest extends InputOutputControllerTest
{
    public function setUp(): void
    {
        parent::setUp();
        $this->inputOutputController = $this->createInputController();
    }

    public function testProcessInput()
    {
        $inputController = $this->createInputController();

        $input = 'take test-item-in-container from test-container-item';
        $response = $inputController->processInput($input);
        $this->assertNotNull($response);
    }
}
