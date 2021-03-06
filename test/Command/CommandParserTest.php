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

    public function testApplyShortcuts()
    {
        $commandParser = $this->createCommandParser();

        $expected = 'go north';
        $input = 'n';
        $command = $commandParser->applyShortcuts($input);
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

    public function testNormalizeTokenSets()
    {
        $commandParser = $this->createCommandParser();

        $expected = [
            'prepositions' => ['a', 'into'],
            'nouns' => ['carrot', 'pot'],
        ];

        $sets = [
            'prepositions' => ['A', 'INTO'],
            'nouns' => ['CARROT', 'pot'],
        ];

        $filtered = $commandParser->normalizeTokenSets($sets);
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

        $command = "put\nthe\ncarrot\ninto\na\npot";
        $tokens = $commandParser->parseCommand($command);
        $this->assertEquals($expected, $tokens);

        $command = "put\tthe\tcarrot\tinto\ta\tpot";
        $tokens = $commandParser->parseCommand($command);
        $this->assertEquals($expected, $tokens);
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
        $shortcuts = [
            'n' => 'go north',
            's' => 'go south',
            'e' => 'go east',
            'w' => 'go west',
        ];
        $phrases = [];
        $locationPhrases = [];

        return new CommandParser(
            $verbs,
            $nouns,
            $articles,
            $prepositions,
            $aliases,
            $shortcuts,
            $phrases,
            $locationPhrases
        );
    }
}
