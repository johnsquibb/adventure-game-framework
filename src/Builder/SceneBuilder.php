<?php

namespace AdventureGame\Builder;

use AdventureGame\Item\Container;
use AdventureGame\Item\ContainerItem;
use AdventureGame\Item\Item;
use AdventureGame\Location\Location;
use AdventureGame\Location\Portal;
use AdventureGameMarkupLanguage\Hydrator\ContainerEntityHydrator;
use AdventureGameMarkupLanguage\Hydrator\ItemEntityHydrator;
use AdventureGameMarkupLanguage\Hydrator\LocationEntityHydrator;
use AdventureGameMarkupLanguage\Hydrator\PortalEntityHydrator;
use AdventureGameMarkupLanguage\Transpiler;

class SceneBuilder
{
    private array $items = [];
    private array $locations = [];
    private array $containers = [];
    private array $portals = [];

    public function __construct(private Transpiler $transpiler)
    {
    }

    public function getContainers(): array
    {
        return $this->containers;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getLocations(): array
    {
        return $this->locations;
    }

    public function getPortals(): array
    {
        return $this->portals;
    }

    public function transpileMarkup(string $markup)
    {
        $hydrators = $this->transpiler->transpile($markup);
        $this->buildItems($hydrators);
        $this->buildContainers($hydrators);
        $this->buildPortals($hydrators);
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

    private function buildContainers(array $hydrators): void
    {
        foreach ($hydrators as $hydrator) {
            if ($hydrator instanceof ContainerEntityHydrator) {
                $container = $this->buildContainer($hydrator);
                $this->containers[$container->getId()] = $container;
            }
        }
    }

    private function buildPortals(array $hydrators): void
    {
        foreach ($hydrators as $hydrator) {
            if ($hydrator instanceof PortalEntityHydrator) {
                $portal = $this->buildPortal($hydrator);
                $this->portals[$portal->getId()] = $portal;
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
        $item->setAcquirable($hydrator->getAcquirable());
        $item->setDeactivatable($hydrator->getDeactivatable());
        $item->setReadable($hydrator->getReadable());
        $item->setLines($hydrator->getText());

        return $item;
    }

    private function buildContainer(ContainerEntityHydrator $hydrator): ContainerItem
    {
        $id = $hydrator->getId();
        $name = $hydrator->getName();
        $description = $hydrator->getDescription();
        $tags = $hydrator->getTags();

        $container = new ContainerItem($id, $name, $description, $tags);

        $container->setSize($hydrator->getSize());
        $container->setActivatable($hydrator->getActivatable());
        $container->setAcquirable($hydrator->getAcquirable());
        $container->setDeactivatable($hydrator->getDeactivatable());
        $container->setReadable($hydrator->getReadable());
        $container->setLines($hydrator->getText());

        $items = $hydrator->getItems();
        $capacity = $hydrator->getCapacity();

        foreach ($items as $itemId) {
            if (isset($this->items[$itemId])) {
                $container->addItem($this->items[$itemId]);
            }

            if (isset($this->containers[$itemId])) {
                $container->addItem($this->containers[$itemId]);
            }
        }

        // Set capacity after applying items to ensure architect can overfill.
        $container->setCapacity($capacity);

        return $container;
    }

    private function buildPortal(PortalEntityHydrator $hydrator): Portal
    {
        $id = $hydrator->getId();
        $name = $hydrator->getName();
        $description = $hydrator->getDescription();
        $tags = $hydrator->getTags();
        $direction = $hydrator->getDirection();
        $destination = $hydrator->getDestination();

        $portal = new Portal($id, $name, $description, $tags, $direction, $destination);

        $portal->setMutable($hydrator->getMutable());
        $portal->setLocked($hydrator->getLocked());
        $portal->setKeyEntityId($hydrator->getKey());

        return $portal;
    }

    private function buildLocation(LocationEntityHydrator $hydrator): Location
    {
        $id = $hydrator->getId();
        $name = $hydrator->getName();
        $description = $hydrator->getDescription();
        $items = $hydrator->getItems();
        $exits = $hydrator->getExits();
        $capacity = $hydrator->getCapacity();

        $container = new Container();

        // Add items.
        foreach ($items as $itemId) {
            if (isset($this->items[$itemId])) {
                $container->addItem($this->items[$itemId]);
            }

            if (isset($this->containers[$itemId])) {
                $container->addItem($this->containers[$itemId]);
            }
        }

        // Set capacity after applying items to ensure architect can overfill.
        $container->setCapacity($capacity);

        $location = new Location($id, $name, $description, $container, []);

        // Add exits.
        foreach ($exits as $portalId) {
            if (isset($this->portals[$portalId])) {
                $location->addExit($this->portals[$portalId]);
            }
        }

        return $location;
    }
}