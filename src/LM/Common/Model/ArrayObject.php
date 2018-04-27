<?php

declare(strict_types=1);

namespace LM\Common\Model;

use LM\Common\Type\TypeCheckerTrait;
use Serializable;
use UnexpectedValueException;

/**
 * Object that provides the features of an array as an object.
 *
 * @deprecated
 * @todo Delete?
 */
class ArrayObject implements Serializable
{
    use TypeCheckerTrait;

    /** @var int */
    private $currentItemIndex;

    /** @var mixed[] */
    private $items;

    /** @var string */
    private $type;

    /**
     * @param mixed[] $items An array of items to initialise the objec with. The
     * constructor checks they are of the type $type.
     * @param string $type The type of the items of the array object.
     */
    public function __construct(array $items, string $type)
    {
        $this->items = [];
        foreach ($items as $key => $item) {
            $this->checkType($item, $type);
            if ($this->isStringType($type)) {
                $this->items[$key] = $item;
            } elseif ($this->isIntegerType($type)) {
                $this->items[$key] = $item;
            } elseif ($this->isClassOrInterfaceName($type)) {
                $this->items[$key] = $item;
            } else {
                throw new UnexpectedValueException();
            }
        }
        $this->currentItemIndex = 0;
        $this->type = $type;
    }

    /**
     * Returns a copy of the object with a value added to it.
     *
     * @param mixed $value The item to add to the array object.
     * @todo Rename to append.
     * @todo Remove $type parameter.
     */
    public function add($value, string $type = null): self
    {
        $this->checkType($value, $this->type);
        $items = $this->items;
        $items[] = $value;

        return new self($items, $this->type);
    }

    /**
     * @todo Remove type parameter.
     */
    public function checkItemsType(string $type = null): self
    {
        foreach ($this->items as $item) {
            $this->checkType($item, $this->type);
        }

        return $this;
    }

    /**
     * @todo Delete
     * @todo Rename to add.
     * @todo Remove type parameter.
     */
    public function addWithkey($key, $value, string $type): self
    {
        $this->checkType($value, $this->type);
        $items = $this->items;
        $items[$key] = $value;

        return new self($items, $this->type);
    }

    /**
     * @return bool Whether the array object's current item has a successor.
     */
    public function hasNextItem(): bool
    {
        return $this->currentItemIndex + 1 < count($this->items);
    }

    /**
     * @param string $key The key of the item.
     * @todo Remove type parameter.
     */
    public function get($key, string $type = null)
    {
        $item = $this->items[$key];
        $this->checkType($item, $this->type);

        return $item;
    }

    /**
     * @return $mixed The current item.
     */
    public function getCurrentItem(string $class)
    {
        $currentItem = $this->items[$this->currentItemIndex];
        $this->checkType($currentItem, $class);

        return $currentItem;
    }

    /**
     * @todo Mutable object!
     */
    public function setToNextItem(): void
    {
        $this->currentItemIndex++;
    }

    /**
     * @return int The number of items the object holds.
     */
    public function getSize(): int
    {
        return count($this->items);
    }

    /**
     * @return array An array representation of the object.
     */
    public function toArray(string $type): array
    {
        foreach ($this->items as $item) {
            $this->checkType($item, $this->type);
        }

        return $this->items;
    }

    public function serialize(): string
    {
        return serialize([
            $this->currentItemIndex,
            $this->items,
            $this->type,
        ]);
    }

    public function unserialize($serialized): void
    {
        list(
            $this->currentItemIndex,
            $this->items,
            $this->type) = unserialize($serialized);
    }
}
