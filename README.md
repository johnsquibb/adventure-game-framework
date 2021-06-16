# adventure-game-framework

A PHP framework for console text adventure games.

Build and play games that use text commands such as:

```
> go north

> look inside the chest

> take key from chest

> unlock the cellar door

> turn flashlight on
```
## Installation

Run `composer install` from the project directory.

## Tests

Run the tests with the `composer test` command.

## Examples

### Demo Game

The [examples directory](examples/demo-game) contains a game that demonstrates the framework
abilities. Run the game on the command line using `composer example` command.

In the same directory is a [diagram](examples/demo-game/docs/Example%20Game%20Map.png) showing the
layout of the demo game.

## How it Works

The [Game Execution](docs/Game%20Execution.png) diagram shows how the framework components interact
during a game run. The example game's [main.php](examples/demo-game/main.php) demonstrates how a 
game can be set up and run.

## TODO

* Basic Logging - errors, warnings, etc.
* Stats tracking - number of moves, number of visits to location, number of item uses, etc.
* Improve save/load system to allow for multiple files using choice system.
