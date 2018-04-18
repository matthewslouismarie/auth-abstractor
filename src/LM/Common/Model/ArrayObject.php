<?php

namespace LM\Common\Model;

use LM\Authentifier\Model\IU2fRegistration;
use LM\Common\Type\TypeCheckerTrait;
use Serializable;
use UnexpectedValueException;

class ArrayObject implements Serializable
{
    use TypeCheckerTrait;

    private $currentItemIndex;

    private $items;

    private $type;

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
     * @todo Rename to append.
     * @todo Remove $type parameter.
     */
    public function add($value, string $type): self
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

    public function hasNextItem(): bool
    {
        return $this->currentItemIndex + 1 < count($this->items);
    }

    public function get($key, string $type)
    {
        $item = $this->items[$key];
        $this->checkType($item, $this->type);

        return $item;
    }

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

    public function getSize(): int
    {
        return count($this->items);
    }

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
            $this->type
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
