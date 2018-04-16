<?php

namespace LM\Common\DataStructure;

use LM\Common\Enum\Scalar;
use LM\Common\Type\TypeCheckerTrait;

/**
 * @todo Should implement a standardised interface.
 */
class TypedMap
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

    public function toArray(): array
    {
        return $this->items;
    }
}
