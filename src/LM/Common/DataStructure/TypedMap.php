<?php

namespace LM\Common\DataStructure;

use LM\Common\Enum\Scalar;
use LM\Common\Type\TypeCheckerTrait;
use Serializable;

/**
 * @todo Should implement a standardised interface.
 * @todo Force items it contains to be serializable?
 */
class TypedMap implements Serializable
{
    use TypeCheckerTrait;

    private $items;

    public function __construct(array $items)
    {
        foreach (array_keys($items) as $key) {
            $this->checkType($key, Scalar::_STR);
        }
        $this->items = $items;
    }

    public function get(string $key, string $type)
    {
        $value = $this->items[$key];
        $this->checkType($value, $type);

        return $value;
    }

    public function getSize(): int
    {
        return count($this->items);
    }

    /**
     * @todo Add unit-test for it.
     */
    public function set(string $key, $value, string $type): self
    {
        $this->checkType($value, $type);
        $items = $this->items;
        $items[$key] = $value;

        return new self($items);
    }

    /**
     * @todo Add unit-test.
     * @todo Should throw an exception if key already set.
     */
    public function add(string $key, $value, string $type): self
    {
        return $this->set($key, $value, $type);
    }

    public function toArray(): array
    {
        return $this->items;
    }

    public function serialize(): string
    {
        return serialize([
            $this->items,
        ]);
    }

    public function unserialize($serialized): void
    {
        list(
            $this->items) = unserialize($serialized);
    }
}
