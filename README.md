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

## Tests

Run the tests with the `composer test` command.

## Examples

### Demo Game

The [examples directory](examples/demo-game) contains a game that demonstrates the framework
abilities. Run the game on the command line using `composer example` command.

In the same directory is a [diagram](examples/demo-game/Example%20Game%20Map.png) showing the layout
of the demo game.

## TODO

### Major

* Refactor response object to be more generic, return item/location etc. as part of response and use
  the decorators to figure out what messages should be. Create abstraction for output controller
  lines so Command objects don't control the flow of text. descriptions, etc. should be added to a
  template and styling should be handled by the client.
* Create abstraction for game-specific words in commands, e.g. "take", "drop" in VerbNounCommand.

### Minor

* Basic Logging - errors, warnings, etc.
* Stats tracking - number of moves, number of visits to location, number of item uses, etc.
* Improve save/load system to allow for multiple files using choice system.