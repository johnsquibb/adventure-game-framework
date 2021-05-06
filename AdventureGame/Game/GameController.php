<?php

namespace AdventureGame\Game;

class GameController
{
    public function __construct(
        public MapController $mapController,
        public PlayerController $playerController,
    ) {
    }
}