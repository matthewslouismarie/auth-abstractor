<?php

declare(strict_types=1);

namespace LM\Common\DataStructure;

use LM\Common\Enum\Scalar;
use LM\Common\Type\TypeCheckerTrait;
use UnexpectedValueException;

/**
 * This is an implementation of ITypedMap. It is immutable.
 *
 * @internal
 * @todo Force items it contains to be serializable?
 */
class TypedMap implements ITypedMap
{
    use TypeCheckerTrait;

    /** @var array */
    private $items;

    /**
     * @param array $items A scalar array of items to initialise the object
     * with.
     */
    public function __construct(array $items)
    {
        foreach (array_keys($items) as $key) {
            $this->checkType($key, Scalar::_STR);
        }
        $this->items = $items;
    }

    /**
     * @param string $key The key of the item.
     * @param string $type The expected type of the item.
     * @return mixed The item.
     * @throws UnexpectedValueException
     */
    public function get(string $key, string $type)
    {
        $value = $this->items[$key];
        $this->checkType($value, $type);

        return $value;
    }

    /**
     * @return int The number of items in the map.
     */
    public function getSize(): int
    {
        return count($this->items);
    }

    /**
     * Returns a copy of itself with the new item. If an item already exists
     * for the given key, it is overwritten.
     *
     * @param string $key The key.
     * @param mixed $value
     * @param string $type The expected type.
     * @return ITypedMap
     * @todo Add unit-test for it.
     */
    public function set(string $key, $value, string $type): ITypedMap
    {
        $this->checkType($value, $type);
        $items = $this->items;
        $items[$key] = $value;

        return new self($items);
    }

    /**
     * @param string $key The key of the item in the map.
     * @param mixed $value The item to add.
     * @param string $type The expected type of the item.
     * @return ITypedMap The new map.
     * @throws UnexpectedValueException
     * @todo Add unit-test.
     * @todo Should throw an exception if key already set.
     */
    public function add(string $key, $value, string $type): ITypedMap
    {
        return $this->set($key, $value, $type);
    }

    /**
     * @return array An array representation of the object.
     */
    public function toArray(): array
    {
        return $this->items;
    }
}
