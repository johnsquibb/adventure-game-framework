<?php

namespace AdventureGame\Response;

class Description
{
    public function __construct(
        public string $name = '',
        public string $summary = '',
        public string $description = '',
    ) {
    }
}