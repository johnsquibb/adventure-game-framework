<?php

namespace AdventureGame\Game;

/**
 * Class GameController provides methods for accessing common game components.
 * @package AdventureGame\Game
 */
class GameController
{
    public function __construct(
        public MapController $mapController,
        public PlayerController $playerController,
    ) {
    }
}