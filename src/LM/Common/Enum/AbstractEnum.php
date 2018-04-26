<?php

declare(strict_types=1);

namespace LM\Common\Enum;

use InvalidArgumentException;
use ReflectionClass;

/**
 * This is a class that enums can inherit for convenience.
 *
 * Any implementation of this class only needs to define constants, and does
 * not need to define a constructor or any other method.
 *
 * @example Scalar.php An implementation.
 */
abstract class AbstractEnum implements IEnum
{
    private $value;

    /**
     * @return string[] An array of constants defined by the enum.
     *
     * @todo Shouldn't static methods be avoided?
     */
    public static function getConstants()
    {
        $reflectionClass = new ReflectionClass(static::class);

        return $reflectionClass->getConstants();
    }

    /**
     * This constructor saves subclasses of this class the burden of defining
     * a constructor.
     * @param string $value The value to initialise the enum with. It is checked
     * for correctness. (It needs to be among the constants.)
     */
    public function __construct(string $value)
    {
        if (!in_array($value, static::getConstants(), true)) {
            throw new InvalidArgumentException();
        }
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function is(IEnum $enum): bool
    {
        if (get_class($enum) !== get_class($this)) {
            return false;
        }
        if ($this->value !== $enum->getValue()) {
            return false;
        }

        return true;
    }

    public function serialize()
    {
        return serialize($this->value);
    }

    public function unserialize($serialized)
    {
        $this->value = unserialize($serialized);
    }
}
