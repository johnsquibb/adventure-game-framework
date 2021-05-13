# adventure-game-framework
A PHP framework for text-based adventure games.

## TODO
* Create abstraction for game-specific words in commands, e.g. "take", "drop" in VerbNounCommand.
* Save and Load games using files.
* Create abstraction for output controller lines so Command objects don't control the flow of text. descriptions, etc. should be added to a template and styling should be handled by the client.