<?php

namespace AdventureGame\Command;

class CommandParser
{
    private array $verbs;
    private array $nouns;
    private array $articles;
    private array $prepositions;
    private array $aliases;
    private array $shortcuts;
    private array $phrases;

    public function __construct(
        array $verbs,
        array $nouns,
        array $articles,
        array $prepositions,
        array $aliases,
        array $shortcuts,
        array $phrases,
    ) {
        $this->verbs = $this->normalizeTokens($verbs);
        $this->nouns = $this->normalizeTokens($nouns);
        $this->articles = $this->normalizeTokens($articles);
        $this->prepositions = $this->normalizeTokens($prepositions);
        $this->aliases = $this->normalizeTokens($aliases);
        $this->shortcuts = $this->normalizeTokens($shortcuts);
        $this->phrases = $this->normalizeTokens($phrases);
    }

    /**
     * Apply shortcuts for literal matches against input.
     * @param string $input
     * @return string
     */
    public function applyShortcuts(string $input): string
    {
        foreach ($this->shortcuts as $match => $shortcut) {
            if ($input === $match) {
                return $shortcut;
            }
        }

        return $this->normalizeString($input);
    }

    /**
     * Apply phrases for literal matches against portions of the input.
     * @param string $input
     * @return string
     */
    public function applyPhrases(string $input): string
    {
        foreach ($this->phrases as $match => $phrase) {
            $input = str_ireplace($match, $phrase, $input);
        }

        return $this->normalizeString($input);
    }

    /**
     * Filter tokens by removing irrelevant words.
     * @param array $tokens
     * @return array
     */
    public function filterTokens(array $tokens): array
    {
        $keep = [];

        foreach ($tokens as $token) {
            if (!in_array($token, $this->articles)) {
                $keep[] = $token;
            }
        }

        return $keep;
    }

    /**
     * Normalize tokens for consistency.
     * @param array $tokens
     * @return array
     */
    public function normalizeTokens(array $tokens): array
    {
        $normal = [];

        foreach ($tokens as $key => $token) {
            $normal[$key] = $this->normalizeString($token);
        }

        return $normal;
    }

    /**
     * Normalize string.
     * @param string $string
     * @return string
     */
    public function normalizeString(string $string): string
    {
        return strtolower($string);
    }

    /**
     * Parse a command into tokens.
     * @param string $command
     * @return array
     */
    public function parseCommand(string $command): array
    {
        return preg_split("/[\s,]+/", trim($command));
    }

    /**
     * Assemble command from tokens.
     * @param array $tokens
     * @return string
     */
    public function assembleCommandFromTokens(array $tokens): string
    {
        return trim(implode(' ', $tokens));
    }

    /**
     * Replace any aliases with preferred token.
     * @param array $tokens
     * @return array
     */
    public function replaceAliases(array $tokens): array
    {
        $alias = [];

        foreach ($tokens as $token) {
            if (array_key_exists($token, $this->aliases)) {
                $alias[] = $this->aliases[$token];
            } else {
                $alias[] = $token;
            }
        }

        return $alias;
    }

    /**
     * Validate parsed tokens using supported words lists.
     * @param array $tokens
     * @return bool true when all tokens are valid, false when first invalid token is encountered.
     */
    public function validateTokens(array $tokens): bool
    {
        foreach ($tokens as $token) {
            if (!$this->isVerb($token)
                && !$this->isNoun($token)
                && !$this->isArticle($token)
                && !$this->isPreposition($token)
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if token is a verb.
     * @param string $token true when token is verb, false otherwise
     * @return bool
     */
    public function isVerb(string $token): bool
    {
        return in_array($token, $this->verbs);
    }

    /**
     * Check if token is a noun.
     * @param string $token true when token is noun, false otherwise
     * @return bool
     */
    public function isNoun(string $token): bool
    {
        return in_array($token, $this->nouns);
    }

    /**
     * Check if token is an article.
     * @param string $token true when token is article, false otherwise
     * @return bool
     */
    public function isArticle(string $token): bool
    {
        return in_array($token, $this->articles);
    }

    /**
     * Check if token is a preposition.
     * @param string $token true when token is preposition, false otherwise
     * @return bool
     */
    public function isPreposition(string $token): bool
    {
        return in_array($token, $this->prepositions);
    }
}