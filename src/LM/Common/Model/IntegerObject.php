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
    private $integer;

    public function __construct(int $integer)
    {
        $this->integer = $integer;
    }

    public function getInteger(): int
    {
        return $this->integer;
    }

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
