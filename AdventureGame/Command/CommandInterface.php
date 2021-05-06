<?php

namespace AdventureGame\Command;

interface CommandInterface
{
    public function process(): void;
}