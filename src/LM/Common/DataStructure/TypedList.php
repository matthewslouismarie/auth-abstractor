<?php

declare(strict_types=1);

namespace LM\Common\DataStructure;

use Traversable;
use ArrayIterator;
use LM\Common\Type\Type;

/**
 * Immutable, typed list.
 */
final class TypedList implements IImmutableList
{
    private $items;

    private $type;

    /**
     * @param array $items An array of items of the specified type.
     * @param string $type The specified type. Can be a scalar type, a class
     * name or an interface.
     * @throws InvalidArgumentException if $items is invalid.
     * @todo $type should maybe have a IType type?
     */
    public function __construct(array $items, string $type)
    {
        $this->type = new Type($type);
        $this->items = [];
        foreach ($items as $item) {
            $this->type->check($item);
            $this->items[] = $item;
        }
    }

    public function append($item): IImmutableList
    {
        $items = $this->items;
        $items[] = $item;

        return new self($items, 'string');
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    public function serialize()
    {
        return serialize($this->items);
    }

    public function unserialize($serialized)
    {
        $this->items = unserialize($serialized);
    }
}
