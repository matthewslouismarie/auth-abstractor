<?php

declare(strict_types=1);

namespace LM\Common\Type;

use InvalidArgumentException;
use LM\Common\Enum\Scalar;

/**
 * Trait for checking types.
 *
 * @todo Make it implement an interface.
 * @todo Rely on an implementation (Scalar).
 */
class Type
{
    /** @var string The type identifier (class name, scalar type, interface…) */
    private $type;

    public function __construct(string $type)
    {
        $this->type = $type;
        if ($this->isArrayType()
        || ($this->isStringType())
        || ($this->isIntegerType())
        || ($this->isBoolType())
        || ($this->isClassOrInterfaceName())) {
        } else {
            throw new InvalidArgumentException();
        }
    }

    /**
     * @return bool Whether the type is the array scalar type.
     */
    public function isArrayType(): bool
    {
        return Scalar::_ARRAY === $this->type;
    }

    /**
     * @return bool Whether the type is the boolean scalar type.
     */
    public function isBoolType(): bool
    {
        return Scalar::_BOOL === $this->type;
    }

    /**
     * @return bool Whether the type is the FQCN of a class or of an interface.
     */
    public function isClassOrInterfaceName(): bool
    {
        return class_exists($this->type) || interface_exists($this->type);
    }

    /**
     * @return bool Whether the type is the scalar integer type.
     */
    public function isIntegerType(): bool
    {
        return Scalar::_INT === $this->type;
    }

    /**
     * @return bool Whether the type is the string type.
     */
    public function isStringType(): bool
    {
        return Scalar::_STR === $this->type;
    }

    /**
     * Throws an exception if the value is not of the expected type.
     * @param mixed $value The value to check.
     * @throws InvalidArgumentException if the value is not of the specified
     * type.
     * @todo Use is_a or instanceof instead?
     */
    public function check($value): void
    {
        if ($this->isArrayType($this->type)) {
            if (!is_array($value)) {
                throw new InvalidArgumentException();
            }
        } elseif ($this->isStringType($this->type)) {
            if (!is_string($value)) {
                throw new InvalidArgumentException();
            }
        } elseif ($this->isIntegerType($this->type)) {
            if (!is_int($value)) {
                throw new InvalidArgumentException();
            }
        } elseif ($this->isBoolType($this->type)) {
            if (!is_bool($value)) {
                throw new InvalidArgumentException();
            }
        } elseif ($this->isClassOrInterfaceName($this->type)) {
            if (!is_object($value) || !is_a($value, $this->type)) {
                throw new InvalidArgumentException();
            }
        } else {
            throw new InvalidArgumentException();
        }
    }
}
