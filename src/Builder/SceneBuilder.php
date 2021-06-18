<?php

namespace AdventureGame\Builder;

use AdventureGame\Event\EventInterface;
use AdventureGame\Event\Events\ActivateItemEvent;
use AdventureGame\Event\Events\DeactivateItemEvent;
use AdventureGame\Event\Events\EnterLocationEvent;
use AdventureGame\Event\Events\HasActivatedItemEvent;
use AdventureGame\Event\TriggerInterface;
use AdventureGame\Event\Triggers\ActivatorPortalLockTrigger;
use AdventureGame\Event\Triggers\AddItemToInventoryUseTrigger;
use AdventureGame\Event\Triggers\AddItemToLocationUseTrigger;
use AdventureGame\Event\Triggers\AddLocationToMapUseTrigger;
use AdventureGame\Event\Triggers\Comparisons\ActivatedComparison;
use AdventureGame\Item\Container;
use AdventureGame\Item\ContainerItem;
use AdventureGame\Item\Item;
use AdventureGame\Item\ItemInterface;
use AdventureGame\Location\Location;
use AdventureGame\Location\Portal;
use AdventureGameMarkupLanguage\Exception\InvalidTypeException;
use AdventureGameMarkupLanguage\Hydrator\ContainerEntityHydrator;
use AdventureGameMarkupLanguage\Hydrator\EventEntityHydrator;
use AdventureGameMarkupLanguage\Hydrator\ItemEntityHydrator;
use AdventureGameMarkupLanguage\Hydrator\LocationEntityHydrator;
use AdventureGameMarkupLanguage\Hydrator\PortalEntityHydrator;
use AdventureGameMarkupLanguage\Hydrator\TriggerEntityHydrator;
use AdventureGameMarkupLanguage\Transpiler;
use Exception;

/**
 * Class SceneBuilder builds world entities from hydrators populated by parsing AGML files.
 * @see https://github.com/johnsquibb/adventure-game-markup-language to learn more about AGML.
 * @package AdventureGame\Builder
 */
class SceneBuilder
{
    const ACTIVATOR_ACTIVATED = 'on';

    private array $items = [];
    private array $locations = [];
    private array $containers = [];
    private array $portals = [];
    private array $triggers = [];
    private array $events = [];

    public function __construct(private Transpiler $transpiler)
    {
    }

    public function getContainers(): array
    {
        return $this->containers;
    }

    public function getEvents(): array
    {
        return $this->events;
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

    public function getTriggers(): array
    {
        return $this->triggers;
    }

    /**
     * Create game entities from AGML.
     * @param string $markup
     * @throws InvalidTypeException
     */
    public function transpileMarkup(string $markup)
    {
        $hydrators = $this->transpiler->transpile($markup);
        $this->buildItems($hydrators);
        $this->buildContainers($hydrators);
        $this->buildPortals($hydrators);
        $this->buildLocations($hydrators);
        $this->buildTriggers($hydrators);
        $this->buildEvents($hydrators);
    }

    /**
     * Build items.
     * @param array $hydrators
     */
    private function buildItems(array $hydrators): void
    {
        foreach ($hydrators as $hydrator) {
            if ($hydrator instanceof ItemEntityHydrator) {
                $item = $this->buildItem($hydrator);
                $this->items[$item->getId()] = $item;
            }
        }
    }

    /**
     * Build an item from hydrator context.
     * @param ItemEntityHydrator $hydrator
     * @return Item
     */
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

    /**
     * Build containers.
     * @param array $hydrators
     */
    private function buildContainers(array $hydrators): void
    {
        foreach ($hydrators as $hydrator) {
            if ($hydrator instanceof ContainerEntityHydrator) {
                $container = $this->buildContainer($hydrator);
                $this->containers[$container->getId()] = $container;
            }
        }
    }

    /**
     * Build a container from hydrator context.
     * @param ContainerEntityHydrator $hydrator
     * @return ContainerItem
     */
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

    /**
     * Build portals.
     * @param array $hydrators
     */
    private function buildPortals(array $hydrators): void
    {
        foreach ($hydrators as $hydrator) {
            if ($hydrator instanceof PortalEntityHydrator) {
                $portal = $this->buildPortal($hydrator);
                $this->portals[$portal->getId()] = $portal;
            }
        }
    }

    /**
     * Build a portal from hydrator context.
     * @param PortalEntityHydrator $hydrator
     * @return Portal
     */
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

    /**
     * Build locations.
     * @param array $hydrators
     */
    private function buildLocations(array $hydrators): void
    {
        foreach ($hydrators as $hydrator) {
            if ($hydrator instanceof LocationEntityHydrator) {
                $location = $this->buildLocation($hydrator);
                $this->locations[$location->getId()] = $location;
            }
        }
    }

    /**
     * Build a location from hydrator context.
     * @param LocationEntityHydrator $hydrator
     * @return Location
     */
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

    /**
     * Build triggers.
     * @param array $hydrators
     */
    private function buildTriggers(array $hydrators): void
    {
        foreach ($hydrators as $hydrator) {
            if ($hydrator instanceof TriggerEntityHydrator) {
                $trigger = $this->buildTrigger($hydrator);
                if ($trigger instanceof TriggerInterface) {
                    $this->triggers[$trigger->getId()] = $trigger;
                }
            }
        }
    }

    /**
     * Build a trigger from hydrator context.
     * @param TriggerEntityHydrator $hydrator
     * @return TriggerInterface|null
     * @throws Exception
     */
    private function buildTrigger(TriggerEntityHydrator $hydrator): ?TriggerInterface
    {
        $trigger = null;

        switch ($hydrator->getType()) {
            case 'ActivatorPortalLockTrigger':
                $portal = $this->portals[$hydrator->getPortal()] ?? null;

                if ($portal instanceof Portal) {
                    $activators = $this->getActivators($hydrator->getActivators());
                    $comparisons = $this->buildActivatedComparisons($hydrator->getComparisons());
                    $trigger = new ActivatorPortalLockTrigger($activators, $comparisons, $portal);
                }
                break;
            case 'AddItemToLocationUseTrigger':
                $item = $this->items[$hydrator->getItem()] ?? null;
                if ($item instanceof ItemInterface) {
                    $trigger = new AddItemToLocationUseTrigger($item, $hydrator->getUses());
                }
                break;
            case 'AddItemToInventoryUseTrigger':
                $item = $this->items[$hydrator->getItem()] ?? null;
                if ($item instanceof ItemInterface) {
                    $trigger = new AddItemToInventoryUseTrigger($item, $hydrator->getUses());
                }
                break;
            case 'AddLocationToMapUseTrigger':
                $location = $this->locations[$hydrator->getLocation()] ?? null;
                if ($location instanceof Location) {
                    $trigger = new AddLocationToMapUseTrigger($location, $hydrator->getUses());

                    // Add door leading from destination to the added location.
                    $portal = $this->portals[$hydrator->getPortal()] ?? null;
                    $destination = $this->locations[$hydrator->getDestination()] ?? null;
                    if (isset($portal, $destination)) {
                        $trigger->addExit($destination->getId(), $portal);
                    }
                }
                break;
        }

        if ($trigger instanceof TriggerInterface) {
            $trigger->setId($hydrator->getId());
        }

        return $trigger;
    }

    /**
     * Get items that match a set of activator identifiers.
     * @param array $activatorIds
     * @return array
     */
    private function getActivators(array $activatorIds): array
    {
        $activators = [];

        foreach ($activatorIds as $itemId) {
            if (isset($this->items[$itemId])) {
                $activators[] = $this->items[$itemId];
            }

            if (isset($this->containers[$itemId])) {
                $activators[] = $this->containers[$itemId];
            }
        }

        return $activators;
    }

    /**
     * Build comparisons from a set of values.
     * @param array $values
     * @return array
     */
    private function buildActivatedComparisons(array $values): array
    {
        $comparisons = [];

        foreach ($values as $value) {
            $comparisons[] = new ActivatedComparison(
                strtolower($value) === self::ACTIVATOR_ACTIVATED
            );
        }

        return $comparisons;
    }

    /**
     * Build events.
     * @param array $hydrators
     */
    private function buildEvents(array $hydrators): void
    {
        foreach ($hydrators as $hydrator) {
            if ($hydrator instanceof EventEntityHydrator) {
                $event = $this->buildEvent($hydrator);
                if ($event instanceof EventInterface) {
                    $this->events[$event->getId()] = $event;
                }
            }
        }
    }

    /**
     * Build an event from hydrator context.
     * @param EventEntityHydrator $hydrator
     * @return EventInterface|null
     */
    private function buildEvent(EventEntityHydrator $hydrator): ?EventInterface
    {
        $event = null;

        switch ($hydrator->getType()) {
            case 'ActivateItemEvent':
                $trigger = $this->triggers[$hydrator->getTrigger()] ?? null;
                if ($trigger instanceof TriggerInterface) {
                    $event = new ActivateItemEvent(
                        $trigger,
                        $hydrator->getItem(),
                        $hydrator->getLocation()
                    );
                }
                break;
            case 'DeactivateItemEvent':
                $trigger = $this->triggers[$hydrator->getTrigger()] ?? null;
                if ($trigger instanceof TriggerInterface) {
                    $event = new DeactivateItemEvent(
                        $trigger,
                        $hydrator->getItem(),
                        $hydrator->getLocation()
                    );
                }
                break;
            case 'HasActivatedItemEvent':
                $trigger = $this->triggers[$hydrator->getTrigger()] ?? null;
                if ($trigger instanceof TriggerInterface) {
                    $event = new HasActivatedItemEvent(
                        $trigger,
                        $hydrator->getItem(),
                        $hydrator->getLocation()
                    );
                }
                break;
            case 'EnterLocationEvent':
                $trigger = $this->triggers[$hydrator->getTrigger()] ?? null;
                if ($trigger instanceof TriggerInterface) {
                    $event = new EnterLocationEvent(
                        $trigger,
                        $hydrator->getLocation()
                    );
                }
                break;
        }

        if ($event instanceof EventInterface) {
            $event->setId($hydrator->getId());
        }

        return $event;
    }
}