<?php

namespace AdventureGame\Platform;

class PlatformManifest
{
    // Game management
    private string $saveGameDirectory = '';

    // Vocabulary
    private array $verbs = [];
    private array $nouns = [];
    private array $prepositions = [];
    private array $articles = [];
    private array $aliases = [];
    private array $phrases = [];
    private array $shortcuts = [];

    // Player
    private string $playerName = '';

    // Locations
    private array $locations = [];
    private string $playerSpawnLocationId = '';

    // Events
    private array $events = [];

    public function getEvents(): array
    {
        return $this->events;
    }

    public function getLocations(): array
    {
        return $this->locations;
    }

    public function getPlayerName(): string
    {
        return $this->playerName;
    }

    public function getPlayerSpawnLocationId(): string
    {
        return $this->playerSpawnLocationId;
    }

    public function getSaveGameDirectory(): string
    {
        return $this->saveGameDirectory;
    }

    public function setSaveGameDirectory(string $saveGameDirectory): void
    {
        $this->saveGameDirectory = $saveGameDirectory;
    }

    public function getVerbs(): array
    {
        return $this->verbs;
    }

    public function setVerbs(array $verbs): void
    {
        $this->verbs = $verbs;
    }

    public function getNouns(): array
    {
        return $this->nouns;
    }

    public function setNouns(array $nouns): void
    {
        $this->nouns = $nouns;
    }

    public function getPrepositions(): array
    {
        return $this->prepositions;
    }

    public function setPrepositions(array $prepositions): void
    {
        $this->prepositions = $prepositions;
    }

    public function getArticles(): array
    {
        return $this->articles;
    }

    public function setArticles(array $articles): void
    {
        $this->articles = $articles;
    }

    public function getAliases(): array
    {
        return $this->aliases;
    }

    public function setAliases(array $aliases): void
    {
        $this->aliases = $aliases;
    }

    public function getPhrases(): array
    {
        return $this->phrases;
    }

    public function setPhrases(array $phrases): void
    {
        $this->phrases = $phrases;
    }

    public function getShortcuts(): array
    {
        return $this->shortcuts;
    }

    public function setShortcuts(array $shortcuts): void
    {
        $this->shortcuts = $shortcuts;
    }

    public function setPlayerName(string $playerName): void
    {
        $this->playerName = $playerName;
    }

    public function setPlayerSpawnLocationId(string $playerSpawnLocationId): void
    {
        $this->playerSpawnLocationId = $playerSpawnLocationId;
    }

    public function setEvents(array $events): void
    {
        $this->events = $events;
    }

    public function setLocations(array $locations): void
    {
        $this->locations = $locations;
    }
}