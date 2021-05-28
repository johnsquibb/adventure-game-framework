# adventure-game-framework
A PHP framework for console text-based adventure games.

## Tests
Run the unit tests with the `composer test` command.

## Examples
The examples directory contains a game that demonstrates the framework abilities. Run the game on
the command line using `composer example` command.

## TODO
* Clean up Platform Factory
* Basic Logging - errors, warnings, etc.
* Stats tracking - number of moves, number of visits to location, etc.
* Create abstraction for game-specific words in commands, e.g. "take", "drop" in VerbNounCommand.
* Improve save/load system to allow for multiple files using choice system.
* Create abstraction for output controller lines so Command objects don't control the flow of text. descriptions, etc. should be added to a template and styling should be handled by the client.
