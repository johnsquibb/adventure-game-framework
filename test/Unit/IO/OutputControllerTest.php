<?php

namespace AdventureGame\IO;

use PHPUnit\Framework\TestCase;

class OutputControllerTest extends TestCase
{
    public function testAddLine()
    {
        $outputController = new OutputController();
        $this->assertEquals([], $outputController->getLines());
        $line = 'test';
        $outputController->addLine($line);
        $this->assertEquals([$line], $outputController->getLines());
    }

    public function testAddLineMultiple()
    {
        $outputController = new OutputController();
        $line = 'test';
        $line2 = 'test2';
        $outputController->addLine($line);
        $outputController->addLine($line2);
        $this->assertEquals([$line, $line2], $outputController->getLines());
    }

    public function testAddLines()
    {
        $outputController = new OutputController();
        $lines = ['test', 123];
        $outputController->addLines($lines);
        $this->assertEquals($lines, $outputController->getLines());
    }

    public function testClearLines()
    {
        $outputController = new OutputController();
        $lines = ['test', 123];
        $outputController->addLines($lines);
        $this->assertEquals($lines, $outputController->getLines());
        $outputController->clearLines();
        $this->assertEmpty($outputController->getLines());
    }

    public function testGetLinesAndClear()
    {
        $outputController = new OutputController();
        $lines = ['test', 123];
        $outputController->addLines($lines);
        $this->assertEquals($lines, $outputController->getLinesAndClear());
        $this->assertEmpty($outputController->getLines());
    }
}
