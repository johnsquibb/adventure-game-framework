<?php declare(strict_types=1);
global $configurationDirectory;
global $platformManifest;

$vocabularyDirectory = $configurationDirectory . '/vocabulary';

// Action words like 'take' or 'drop'.
$verbs = json_decode(file_get_contents($vocabularyDirectory . '/verbs.json'), true);

// Things that can be acted upon like 'door', 'key'.
$nouns = json_decode(file_get_contents($vocabularyDirectory . '/nouns.json'), true);

// Mostly arbitrary filler words that identify things and make sentences more natural.
$articles = json_decode(file_get_contents($vocabularyDirectory . '/articles.json'), true);

// Prepositions can add specificity to a command.
// e.g. 'look at chest' will describe it, whereas 'look in chest' will open it.
$prepositions = json_decode(file_get_contents($vocabularyDirectory . '/prepositions.json'), true);

// Aliases are single-word synonyms.
// Key: original word
// Value: substitution.
$aliases = json_decode(file_get_contents($vocabularyDirectory . '/aliases.json'), true);

// Phrases are multiple-word substitutions.
// Key: original phrase
// Value: substitution
$phrases = json_decode(file_get_contents($vocabularyDirectory . '/phrases.json'), true);

// Location phrases are sets of phrases that apply to specific locations.
// This allows for situational phrases, e.g. 'go through door' in 'spawn' means 'move west', but in
// the 'roomWestOfSpawn' location, 'go through door' could be 'move east' instead.
// Key: location id
// Value: phrase array (key: original, value: substitution)
$locationPhrases = json_decode(
    file_get_contents($vocabularyDirectory . '/location_phrases.json'),
    true
);

// Shortcuts provide quick access to full commands.
// Key: shortcut
// Value: command
$shortcuts = json_decode(file_get_contents($vocabularyDirectory . '/shortcuts.json'), true);

// Apply configuration.
$platformManifest->setVerbs($verbs);
$platformManifest->setNouns($nouns);
$platformManifest->setArticles($articles);
$platformManifest->setPrepositions($prepositions);
$platformManifest->setAliases($aliases);
$platformManifest->setPhrases($phrases);
$platformManifest->setLocationPhrases($locationPhrases);
$platformManifest->setShortcuts($shortcuts);