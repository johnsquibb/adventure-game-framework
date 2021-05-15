<?php

namespace AdventureGame\Entity;

/**
 * Interface EntityInterface defines methods for objects that act like entities.
 * @package AdventureGame\Entity
 */
interface EntityInterface
{
    public function getId(): string;

    public function getName(): string;

    public function getDescription(): string;
}