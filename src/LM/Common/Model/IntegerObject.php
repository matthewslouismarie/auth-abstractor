<?php

namespace LM\Common\Model;

use Serializable;
use UnexpectedValueException;

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
