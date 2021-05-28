<?php

namespace AdventureGame\Location;

use AdventureGame\Entity\EntityInterface;
use AdventureGame\Entity\TaggableEntityInterface;
use AdventureGame\Item\ContainerInterface;
use AdventureGame\Traits\DescriptionTrait;
use AdventureGame\Traits\IdentityTrait;
use AdventureGame\Traits\NameTrait;
use AdventureGame\Traits\SerializeTrait;

/**
 * Class Location is a place in which players and objects can exist.
 * @package AdventureGame\Location
 */
class Location implements EntityInterface
{
    use IdentityTrait;
    use NameTrait;
    use DescriptionTrait;

    public function __construct(
        string $id,
        string $name,
        string $description,
        private ContainerInterface $container,
        private array $exits,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
    }

    /**
     * Add an exit.
     * @param Portal $exit
     */
    public function addExit(Portal $exit): void
    {
        $this->exits[] = $exit;
    }

    /**
     * Get container for location.
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * Get first exit by tag, if it exists.
     * @param string $tag
     * @return Portal|null
     */
    public function getExitByTag(string $tag): ?Portal
    {
        foreach ($this->exits as $exit) {
            if (is_a($exit, TaggableEntityInterface::class) && $exit->hasTag($tag)) {
                return $exit;
            }
        }

        return null;
    }

    /**
     * Get exit in specified direction, if it exists.
     * @param string $direction
     * @return Portal|null
     */
    public function getExitInDirection(string $direction): ?Portal
    {
        foreach ($this->exits as $exit) {
            if ($exit->direction === $direction) {
                return $exit;
            }
        }

        return null;
    }

    /**
     * Get all exits for location.
     * @return array A list of Portal objects.
     */
    public function getExits(): array
    {
        return $this->exits;
    }
}