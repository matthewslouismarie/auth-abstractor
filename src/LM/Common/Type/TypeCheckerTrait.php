<?php

namespace LM\Common\Type;

use InvalidArgumentException;

trait TypeCheckerTrait
{
    public function isArrayType(string $type): bool
    {
        return 'array' === $type;
    }

    public function isBoolType(string $type): bool
    {
        return 'boolean' === $type;
    }

    public function isClassName(string $type): bool 
    {
        return class_exists($type);
    }

    public function isIntegerType(string $type): bool
    {
        return 'integer' === $type;
    }

    public function isStringType(string $type): bool
    {
        return 'string' === $type;
    }

    /**
     * @todo Use is_a or instanceof instead?
     */
    public function checkType($value, string $type): void
    {
        if ($this->isArrayType($type)) {
            if (!is_array($value)) {
                throw new InvalidArgumentException();
            }
        } elseif ($this->isStringType($type)) {
            if (!is_string($value)) {
                throw new InvalidArgumentException();
            }
        } elseif($this->isIntegerType($type)) {
            if (!is_int($value)) {
                throw new InvalidArgumentException();
            }
        } elseif($this->isBoolType($type)) {
            if (!is_bool($value)) {
                throw new InvalidArgumentException();
            }
        } elseif ($this->isClassName($type)) {
            if (!is_object($value)
            || get_class($value) !== $type) {
                throw new InvalidArgumentException();
            }       
        } else {
            throw new InvalidArgumentException();
        }
    }
}
