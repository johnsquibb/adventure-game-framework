<?php

namespace AdventureGame\Test\IO;

use AdventureGame\IO\InputOutputControllerInterface;
use AdventureGame\Test\FrameworkTest;

abstract class InputOutputControllerTest extends FrameworkTest
{
    protected InputOutputControllerInterface $inputOutputController;

    public function testAddLine()
    {
        $this->assertEquals([], $this->inputOutputController->getLines());
        $line = 'test';
        $this->inputOutputController->addLine($line);
        $this->assertEquals([$line], $this->inputOutputController->getLines());
    }

    public function testAddLineMultiple()
    {
        $line = 'test';
        $line2 = 'test2';
        $this->inputOutputController->addLine($line);
        $this->inputOutputController->addLine($line2);
        $this->assertEquals([$line, $line2], $this->inputOutputController->getLines());
    }

    public function testAddLines()
    {
        $lines = ['test', 123];
        $this->inputOutputController->addLines($lines);
        $this->assertEquals($lines, $this->inputOutputController->getLines());
    }

    public function testClearLines()
    {
        $lines = ['test', 123];
        $this->inputOutputController->addLines($lines);
        $this->assertEquals($lines, $this->inputOutputController->getLines());
        $this->inputOutputController->clearLines();
        $this->assertEmpty($this->inputOutputController->getLines());
    }

    public function testGetLinesAndClear()
    {
        $lines = ['test', 123];
        $this->inputOutputController->addLines($lines);
        $this->assertEquals($lines, $this->inputOutputController->getLinesAndClear());
        $this->assertEmpty($this->inputOutputController->getLines());
    }
}
