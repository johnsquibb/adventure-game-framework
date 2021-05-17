<?php

namespace AdventureGame\Entity;

/**
 * Interface EntityInterface defines methods for objects that act like entities.
 * @package AdventureGame\Entity
 */
interface EntityInterface
{
    public function getDescription(): string;

    public function getId(): string;

    public function getName(): string;

    public function getSummary(): string;
}