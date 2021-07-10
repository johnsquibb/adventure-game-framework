<?php

declare(strict_types=1);

use AdventureGame\Builder\SceneBuilder;
use AdventureGameMarkupLanguage\Lexer;
use AdventureGameMarkupLanguage\Parser;
use AdventureGameMarkupLanguage\Transpiler;

global $platformManifest;

// Build game world from markup files.
global $libDirectory;
$markup = file_get_contents($libDirectory . '/markup/scene.agml');

$lexer = new Lexer();
$parser = new Parser();
$transpiler = new Transpiler($lexer, $parser);
$builder = new SceneBuilder($transpiler);

$builder->transpileMarkup($markup);
$items = $builder->getItems();
$containers = $builder->getContainers();
$portals = $builder->getPortals();
$locations = $builder->getLocations();
$triggers = $builder->getTriggers();
$events = $builder->getEvents();

// Apply configuration.
$platformManifest->setLocations($locations);
$platformManifest->setEvents($events);

// Add new vocabulary discovered during parsing.
$builder->parseVocabulary();
$phrases = $builder->getPhrases();
$platformManifest->addNouns($builder->getNouns());
$platformManifest->addPhrases($builder->getPhrases());
