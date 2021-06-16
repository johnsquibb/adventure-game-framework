<?php

namespace AdventureGame\Builder;

use AdventureGame\Item\Container;
use AdventureGame\Item\Item;
use AdventureGame\Location\Location;
use AdventureGameMarkupLanguage\Hydrator\ItemEntityHydrator;
use AdventureGameMarkupLanguage\Hydrator\LocationEntityHydrator;
use AdventureGameMarkupLanguage\Transpiler;

class SceneBuilder
{
    private array $items = [];
    private array $locations = [];

    public function __construct(private Transpiler $transpiler)
    {
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getLocations(): array
    {
        return $this->locations;
    }

    public function transpileMarkup(string $markup)
    {
        $hydrators = $this->transpiler->transpile($markup);
        $this->buildItems($hydrators);
        $this->buildLocations($hydrators);
    }

    private function buildItems(array $hydrators): void
    {
        foreach ($hydrators as $hydrator) {
            if ($hydrator instanceof ItemEntityHydrator) {
                $item = $this->buildItem($hydrator);
                $this->items[$item->getId()] = $item;
            }
        }
    }

    private function buildLocations(array $hydrators): void
    {
        foreach ($hydrators as $hydrator) {
            if ($hydrator instanceof LocationEntityHydrator) {
                $location = $this->buildLocation($hydrator);
                $this->locations[$location->getId()] = $location;
            }
        }
    }

    private function buildItem(ItemEntityHydrator $hydrator): Item
    {
        $id = $hydrator->getId();
        $name = $hydrator->getName();
        $description = $hydrator->getDescription();
        $tags = $hydrator->getTags();

        $item = new Item($id, $name, $description, $tags);

        $item->setSize($hydrator->getSize());
        $item->setActivatable($hydrator->getActivatable());
        $item->setDeactivatable($hydrator->getDeactivatable());
        $item->setReadable($hydrator->getReadable());
        $item->setLines($hydrator->getText());

        return $item;
    }

    private function buildLocation(LocationEntityHydrator $hydrator): Location
    {
        $id = $hydrator->getId();
        $name = $hydrator->getName();
        $description = $hydrator->getDescription();
        $exits = $hydrator->getExits();
        $items = $hydrator->getItems();
        $capacity = $hydrator->getCapacity();

        $container = new Container();
        foreach ($items as $itemId) {
            if (isset($this->items[$itemId])) {
                $container->addItem($this->items[$itemId]);
            }
        }

        // Set capacity after applying items to ensure architect can overfill.
        $container->setCapacity($capacity);

        $location = new Location($id, $name, $description, $container, $exits);

        return $location;
    }
}