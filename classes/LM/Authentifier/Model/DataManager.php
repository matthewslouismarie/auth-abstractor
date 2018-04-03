<?php

namespace LM\Authentifier\Model;

use InvalidArgumentException;
use Serializable;
use UnexpectedValueException;
use LM\Authentifier\Exception\KeyAlreadyTakenException;

/**
 * @todo Either does not expose RequestDatum, or expose it and is agnostic of
 * its fields.
 */
class DataManager implements Serializable
{
    private $items;

    public function __construct(array $items = [])
    {
        $this->items = [];
        foreach ($items as $item) {
            if (!$item instanceof IDataHolder) {
                throw new InvalidArgumentException();
            }
            $this->items[] = $item;
        }
    }

    public function add(IDataHolder $item): self
    {
        $items = $this->items;
        $items[] = $item;

        return new self($items);
    }

    public function addUnique(IDataHolder $item, string $property): self
    {
        if ($this->hasSimilar($item, $property)) {
            throw new InvalidArgumentException();
        }

        return $this->add($item);
    }

    public function filterOut(IDataHolder $item, string $property): self
    {
        return new self(
            array_filter(
                $this->items,
                function ($item) use ($key) {
                    return $item->get($property) !== $item->get($property);
                }
            )
        );
    }

    public function get(string $property, $value): self
    {
        return new self(array_filter(
            $this->items,
            function ($item) use ($property, $value) {
                return $item->get($property) === $value;
            }
        ));
    }

    public function getOnlyValue(): RequestDatum
    {
        $items = $this->items;
        if (1 !== count($items)) {
            throw new UnexpectedValueException();
        } else {
            return reset($items);
        }
    }

    public function getSize(): int
    {
        return count($this->items);
    }

    public function has(string $property, string $value): bool
    {
        foreach ($this->items as $currentItem) {
            if ($currentItem->get($property) === $value) {
                return true;
            }
        }

        return false;
    }

    public function isEmpty(): bool
    {
        return 0 === count($this->items);
    }

    public function replace(RequestDatum $newItem, string $property): self
    {
        return $this
            ->filterOutBy($property, $newItem->get($property))
            ->add($newValue)
        ;
    }

    public function toArray(string $property): array
    {
        $values = [];
        foreach ($this->items as $item) {
            $values[] = $item->get($item->get($property));
        }

        return $values;
    }

    public function serialize(): string
    {
        return serialize($this->items);
    }

    public function unserialize($serialized): void
    {
        $this->items = unserialize($serialized);
    }
}