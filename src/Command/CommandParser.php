<?php

namespace AdventureGame\Command;

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