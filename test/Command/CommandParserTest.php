<?php

namespace AdventureGame\Test\Command;

use AdventureGame\Command\CommandParser;
use PHPUnit\Framework\TestCase;

class CommandParserTest extends TestCase
{
    public function testAliasTokens()
    {
        $commandParser = $this->createCommandParser();

        $expected = ['put', 'the', 'carrot', 'into', 'a', 'pot'];

        $command = 'place that vegetable in an oven';
        $tokens = $commandParser->parseCommand($command);
        $filtered = $commandParser->replaceAliases($tokens);
        $this->assertEquals($expected, $filtered);
    }

    public function testApplySubstitutions()
    {
        $commandParser = $this->createCommandParser();

        $expected = 'go north';
        $input = 'n';
        $command = $commandParser->applySubstitutions($input);
        $this->assertEquals($expected, $command);
    }

    public function testFilterTokens()
    {
        $commandParser = $this->createCommandParser();

        $expected = ['put', 'carrot', 'into', 'pot'];

        $command = 'put the carrot into a pot';
        $tokens = $commandParser->parseCommand($command);
        $filtered = $commandParser->filterTokens($tokens);
        $this->assertEquals($expected, $filtered);
    }

    public function testNormalizeTokens()
    {
        $commandParser = $this->createCommandParser();

        $expected = ['put', 'the', 'carrot', 'into', 'a', 'pot'];

        $command = 'Put THE Carrot into A POT';
        $tokens = $commandParser->parseCommand($command);
        $filtered = $commandParser->normalizeTokens($tokens);
        $this->assertEquals($expected, $filtered);
    }

    public function testNormalizeTokensOrderDoesNotMatter()
    {
        $commandParser = $this->createCommandParser();

        $expected = ['the', 'put', 'pot', 'carrot', 'into', 'a'];

        $command = 'THE put pot carrot into a';
        $tokens = $commandParser->parseCommand($command);
        $filtered = $commandParser->normalizeTokens($tokens);
        $this->assertEquals($expected, $filtered);
    }

    public function testParseCommand()
    {
        $commandParser = $this->createCommandParser();

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

    private function createCommandParser()
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
        $substitutions = [
            'n' => 'go north',
        ];

        return new CommandParser(
            $verbs, $nouns, $articles, $prepositions, $aliases, $substitutions
        );
    }

    public function testTokenParsingOrderOfOperations()
    {
        $commandParser = $this->createCommandParser();

        $expected = ['put', 'carrot', 'into', 'pot'];

        $command = 'Put the CARROT into a Pot';
        $tokens = $commandParser->parseCommand($command);
        $tokens = $commandParser->normalizeTokens($tokens);
        $tokens = $commandParser->filterTokens($tokens);
        $tokens = $commandParser->replaceAliases($tokens);

        $this->assertEquals($expected, $tokens);

        $isValid = $commandParser->validateTokens($tokens);
        $this->assertTrue($isValid);
    }

    public function testValidateTokens()
    {
        $commandParser = $this->createCommandParser();

        $command = 'put MUCH carrot into EVERY pot';
        $tokens = $commandParser->parseCommand($command);
        $isValid = $commandParser->validateTokens($tokens);
        $this->assertFalse($isValid);
    }
}
