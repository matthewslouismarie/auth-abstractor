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

    private $currentItemIndex;

    public function __construct(array $items, string $type)
    {
        $this->items = [];
        foreach ($items as $item) {
            $this->checkType($item, $type);
            if ($this->isStringType($type)) {
                $this->items[] = $item;
            } elseif ($this->isIntegerType($type)) {
                $this->items[] = $type;
            } elseif ($this->isClassName($type)) {
                $this->items[] = $item; 
            }
        }
        $this->currentItemIndex = 0;
    }

    public function hasNextItem(): bool
    {
        return $this->currentItemIndex + 1 < count($this->items);
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
        return serialize([
            $this->currentItemIndex,
            $this->items])
        ;
    }

    public function unserialize($serialized): void
    {
        list(
            $this->currentItemIndex,
            $this->items) = unserialize($serialized);
    }
}
