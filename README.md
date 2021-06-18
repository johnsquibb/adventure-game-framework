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

## Unit Tests

A suite of PHPUnit tests is located in the [test directory](test).

Run the unit tests:
`composer test`

## Architecture

The [Game Execution](docs/Game%20Execution.png) diagram shows how the framework components interact
during a game run. The example game's [main.php](examples/demo-game/main.php) demonstrates how a
game can be set up and run.

## Examples

### Demo Game

The [examples directory](examples/demo-game) contains a game that demonstrates the framework
abilities. Run the game on the command line using:

`composer example`

In the same directory is a [diagram](examples/demo-game/docs/Example%20Game%20Map.png) showing the
layout of the demo game.

### Automated Testing

A test client can be used to run automated tests.

Run the example:

`composer example-test`

The test client will play the game by running each of the test cases in the provided order. The
client reports any errors, halting game execution. See [test.php](examples/demo-game/test.php) for
more details.

Optionally specify the number of milliseconds to wait between each test, for
example: `composer example-test 100` to wait 100 milliseconds between each test. The default wait
value is 1000 milliseconds.

## TODO

* Basic Logging - errors, warnings, etc.
* Stats tracking - number of moves, number of visits to location, number of item uses, etc.
* Improve save/load system to allow for multiple files using choice system.
