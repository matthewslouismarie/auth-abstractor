<?php

namespace LM\Common\Model;

use Serializable;
use UnexpectedValueException;

class ArrayObject implements Serializable
{
    private $items;

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    public function toArray(): array
    {
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
