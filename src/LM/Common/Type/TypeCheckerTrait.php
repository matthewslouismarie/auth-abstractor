<?php

declare(strict_types=1);

namespace LM\Common\Type;

use InvalidArgumentException;
use LM\Common\Enum\Scalar;

/**
 * Trait for checking types.
 *
 * @todo Turn it into a class instead?
 */
trait TypeCheckerTrait
{
    /**
     * @param string $type The type to check.
     * @return bool Whether the type is the array scalar type.
     */
    public function isArrayType(string $type): bool
    {
        return Scalar::_ARRAY === $type;
    }

    /**
     * @param string $type The type to check.
     * @return bool Whether the type is the boolean scalar type.
     */
    public function isBoolType(string $type): bool
    {
        return Scalar::_BOOL === $type;
    }

    /**
     * @param string $type The type to check.
     * @return bool Whether the type is the FQCN of a class or of an interface.
     */
    public function isClassOrInterfaceName(string $type): bool
    {
        return class_exists($type) || interface_exists($type);
    }

    /**
     * @param string $type The type to check.
     * @return bool Whether the type is the scalar integer type.
     */
    public function isIntegerType(string $type): bool
    {
        return Scalar::_INT === $type;
    }

    /**
     * @param string $type The type to check.
     * @return bool Whether the type is the string type.
     */
    public function isStringType(string $type): bool
    {
        return Scalar::_STR === $type;
    }

    /**
     * Throws an exception if the value is not of the expected type.
     * @param mixed $value The value to check.
     * @param string $type The type to check against.
     * @throws InvalidArgumentException if the value is not of the specified
     * type.
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
