<?php declare(strict_types=1);

use AdventureGame\Builder\SceneBuilder;
use AdventureGameMarkupLanguage\Lexer;
use AdventureGameMarkupLanguage\Parser;
use AdventureGameMarkupLanguage\Transpiler;

global $platformManifest;

// Example of using Adventure Game Markup Language (AGML) to create an item.
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