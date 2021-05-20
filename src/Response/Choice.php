<?php

namespace AdventureGame\Response;

/**
 * Class Choice provides container for interactive responses with options to be presented to the
 * player.
 * @package AdventureGame\Response
 */
class Choice
{
    private $callback;

    public function __construct(private string $message, private array $options, callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * Return the message to be displayed.
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Return the options to be displayed for player selection.
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Invoke the callback with user input.
     * @param array $params
     */
    public function invoke(array $params): void
    {
        call_user_func($this->callback, $params);
    }
}