<?php

namespace LM\Common\Model;

use InvalidArgumentException;
use LM\Common\Type\TypeCheckerTrait;
use Serializable;
use UnexpectedValueException;

class ArrayObject implements Serializable
{
    use TypeCheckerTrait;

    private $items;

    public function __construct(array $items, string $type)
    {
        $this->items = [];
        foreach ($items as $item) {
            $this->checkType($item, $type);
            if ($this->isStringType($type)) {
                $this->items[] = new StringObject($item);
            } elseif ($this->isClassName($type)) {
                $this->items = $items; 
            }
        }
    }

    public function getCurrentItem(string $class)
    {
        $this->checkType(current($this->items), $class);

        return current($this->items);
    }

    public function setToNextItem(): void
    {
        next($this->items);
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
