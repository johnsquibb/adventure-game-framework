<?php

namespace AdventureGame\Command;

use AdventureGame\Command\Exception\InvalidCommandException;

class CommandParser
{
    public function __construct(
        private array $verbs,
        private array $nouns,
        private array $articles,
        private array $prepositions,
        private array $aliases,
    ) {
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

        foreach ($tokens as $token) {
            $normal[] = strtolower($token);
        }

        return $normal;
    }

    /**
     * Validate parsed tokens using supported words lists.
     * @param array $tokens
     * @return bool
     * @throws InvalidCommandException
     */
    public function validateTokens(array $tokens): bool
    {
        foreach ($tokens as $token) {
            if (!in_array($token, $this->verbs)
                && !in_array($token, $this->nouns)
                && !in_array($token, $this->articles)
                && !in_array($token, $this->prepositions)
            ) {
                throw new InvalidCommandException("Invalid token {$token}");
            }
        }
    }
}