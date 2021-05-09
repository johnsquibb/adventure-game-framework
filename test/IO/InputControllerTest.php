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

        $expected = [
            'Added Test Item 2 to inventory'
        ];

        $input = 'take test-item-in-container from test-container-item';
        $result = $inputController->processInput($input);
        $this->assertTrue($result);
        $lines = $inputController->commandController->commandFactory->outputController->getLines();
        $this->assertEquals($expected, $lines);
    }
}
