<?php

namespace LM\Authentifier\Enum;

use InvalidArgumentException;

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

    public function __construct($value)
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

    public function is(AbstractEnum $enum): bool
    {
        return $this->value === $enum->getValue()
        && get_class($enum) === static::class;
    }
}
