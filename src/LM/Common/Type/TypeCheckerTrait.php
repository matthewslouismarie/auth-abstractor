<?php

namespace LM\Common\Type;

use InvalidArgumentException;
use LM\Common\Enum\Scalar;

trait TypeCheckerTrait
{
    public function isArrayType(string $type): bool
    {
        return Scalar::_ARRAY === $type;
    }

    public function isBoolType(string $type): bool
    {
        return Scalar::_BOOL === $type;
    }

    public function isClassOrInterfaceName(string $type): bool
    {
        return class_exists($type) || interface_exists($type);
    }

    public function isIntegerType(string $type): bool
    {
        return Scalar::_INT === $type;
    }

    public function isStringType(string $type): bool
    {
        return Scalar::_STR === $type;
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
        } elseif ($this->isIntegerType($type)) {
            if (!is_int($value)) {
                throw new InvalidArgumentException();
            }
        } elseif ($this->isBoolType($type)) {
            if (!is_bool($value)) {
                throw new InvalidArgumentException();
            }
        } elseif ($this->isClassOrInterfaceName($type)) {
            if (!is_object($value) || !is_a($value, $type)) {
                throw new InvalidArgumentException();
            }
        } else {
            throw new InvalidArgumentException();
        }
    }
}
