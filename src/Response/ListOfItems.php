<?php

namespace AdventureGame\Response;

use AdventureGame\Entity\ActivatableEntityInterface;
use AdventureGame\Item\ItemInterface;

class ListOfItems
{
    public const ACTION_ACTIVATE = 'activate';
    public const ACTION_DEACTIVATE = 'deactivate';
    public const ACTION_DROP = 'drop';
    public const ACTION_READ = 'read';
    public const ACTION_TAKE = 'take';

    public function __construct(private array $items, private string $messageType = '')
    {
    }

    public function getResponse(): Response
    {
        $response = new Response();
        $response->addMessage($this->getMessage());
        foreach ($this->items as $item) {
            $response->addItemSummaryWithTag($this->listItem($item));
        }
        return $response;
    }

    private function getMessage(): string
    {
        return match ($this->messageType) {
            self::ACTION_DROP => 'Which item do you want to drop?',
            self::ACTION_TAKE => 'Which item do you want to take?',
            self::ACTION_READ => 'Which item do you want to read?',
            self::ACTION_ACTIVATE => 'Which item do you want to activate?',
            self::ACTION_DEACTIVATE => 'Which item do you want to deactivate?',
            default => 'Which item?',
        };
    }

    /**
     * List an item's name.
     * @param ItemInterface $item
     * @return ItemDescription
     */
    protected function listItem(ItemInterface $item): ItemDescription
    {
        $description = new ItemDescription(
            $item->getName(),
            $item->getSummary(),
            $item->getDescription(),
            $item->getTags()
        );

        if ($item instanceof ActivatableEntityInterface) {
            if ($item->getActivated()) {
                $description->setStatus('activated');
            }
        }

        return $description;
    }
}
