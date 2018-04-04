<?php

namespace LM\Common\Model;

use Serializable;
use UnexpectedValueException;
use InvalidArgumentException;

class ArrayObject implements Serializable
{
    private $items;

    public function __construct(array $items, string $class)
    {
        foreach ($items as $item) {
            if (get_class($item) !== $class) {
                throw new InvalidArgumentException();
            }
        }
        $this->items = $items;
    }

    public function getSize(): int
    {
        return count($this->items);
    }

    public function toArray(string $class): array
    {
        foreach ($this->items as $item) {
            if (get_class($item) !== $class) {
                throw new UnexpectedValueException();
            }
        }

        return $this->items;
    }

    public function serialize(): string
    {
        return serialize($this->items);
    }

    public function unserialize($serialized): void
    {
        $unserialized = unserialize($serialized);
        if (is_array($unserialized)) {
            $this->items = $unserialized;
        } else {
            throw new UnexpectedValueException();
        }
    }
}
