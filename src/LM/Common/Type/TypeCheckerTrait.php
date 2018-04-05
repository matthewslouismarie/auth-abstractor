<?php

namespace LM\Common\Type;

use InvalidArgumentException;

trait TypeCheckerTrait
{
    public function isStringType(string $type): bool
    {
        return "string" === $type;
    }

    public function isClassName(string $className): bool 
    {
        return class_exists($className);
    }

    /**
     * @todo Use is_a or instanceof instead?
     */
    public function checkType($value, string $type): void
    {
        if ($this->isStringType($type)) {
            if (!is_string($value)) {
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
