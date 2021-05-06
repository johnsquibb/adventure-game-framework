<?php

namespace AdventureGame\Command;

use AdventureGame\Command\Exception\InvalidCommandException;
use PHPUnit\Framework\TestCase;

class CommandParserTest extends TestCase
{
    private function getCommandParser()
    {
        $verbs = ['put'];
        $nouns = ['carrot', 'pot'];
        $articles = ['the', 'a'];
        $prepositions = ['into'];
        $aliases = [
            'place' => 'put',
            'that' => 'the',
            'vegetable' => 'carrot',
            'oven' => 'pot',
            'an' => 'a',
            'in' => 'into',
        ];

        return new CommandParser($verbs, $nouns, $articles, $prepositions, $aliases);
    }

    public function testParseCommand()
    {
        $commandParser = $this->getCommandParser();

        $expected = ['put', 'the', 'carrot', 'into', 'a', 'pot'];

        $command = 'put the carrot into a pot';
        $tokens = $commandParser->parseCommand($command);
        $this->assertEquals($expected, $tokens);

        $command = 'put   the    carrot into a pot   ';
        $tokens = $commandParser->parseCommand($command);
        $this->assertEquals($expected, $tokens);

        $command = 'put,the,carrot,into,a,pot';
        $tokens = $commandParser->parseCommand($command);
        $this->assertEquals($expected, $tokens);

        $command = "put\nthe\ncarrot\ninto\na\npot";
        $tokens = $commandParser->parseCommand($command);
        $this->assertEquals($expected, $tokens);

        $command = "put\tthe\tcarrot\tinto\ta\tpot";
        $tokens = $commandParser->parseCommand($command);
        $this->assertEquals($expected, $tokens);
    }

    public function testValidateTokens()
    {
        $commandParser = $this->getCommandParser();

        $command = 'put MUCH carrot into EVERY pot';
        $tokens = $commandParser->parseCommand($command);
        $this->expectException(InvalidCommandException::class);
        $commandParser->validateTokens($tokens);
    }

    public function testNormalizeTokens()
    {
        $commandParser = $this->getCommandParser();

        $expected = ['put', 'the', 'carrot', 'into', 'a', 'pot'];

        $command = 'Put THE Carrot into A POT';
        $tokens = $commandParser->parseCommand($command);
        $filtered = $commandParser->normalizeTokens($tokens);
        $this->assertEquals($expected, $filtered);
    }

    public function testAliasTokens()
    {
        $commandParser = $this->getCommandParser();

        $expected = ['put', 'the', 'carrot', 'into', 'a', 'pot'];

        $command = 'place that vegetable in an oven';
        $tokens = $commandParser->parseCommand($command);
        $filtered = $commandParser->replaceAliases($tokens);
        $this->assertEquals($expected, $filtered);
    }

    public function testFilterTokens()
    {
        $commandParser = $this->getCommandParser();

        $expected = ['put', 'carrot', 'into', 'pot'];

        $command = 'put the carrot into a pot';
        $tokens = $commandParser->parseCommand($command);
        $filtered = $commandParser->filterTokens($tokens);
        $this->assertEquals($expected, $filtered);
    }
}
