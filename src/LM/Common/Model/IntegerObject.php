<?php

declare(strict_types=1);

namespace LM\Common\Model;

use Serializable;
use UnexpectedValueException;

/**
 * Immutable object that represents an integer.
 */
class IntegerObject implements Serializable
{
    /** @var int */
    private $integer;

    /**
     * @param int $integer The value to initialise the object with.
     */
    public function __construct(int $integer)
    {
        $this->integer = $integer;
    }

    /**
     * @return int The integer value of the object.
     */
    public function toInteger(): int
    {
        return $this->integer;
    }

    public function serialize(): string
    {
        return serialize($this->integer);
    }

    public function unserialize($serialized): void
    {
        $unserialized = unserialize($serialized);
        if (is_int($unserialized)) {
            $this->integer = $unserialized;
        } else {
            throw new UnexpectedValueException();
        }
    }
}
