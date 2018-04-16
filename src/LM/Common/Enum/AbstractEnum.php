<?php

namespace LM\Common\Enum;

use InvalidArgumentException;
use ReflectionClass;

abstract class AbstractEnum implements IEnum
{
    private $value;

    /**
     * @todo Shouldn't static methods be avoided?
     */
    public static function getConstants()
    {
        $reflectionClass = new ReflectionClass(static::class);

        return $reflectionClass->getConstants();
    }

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
