<?php

declare(strict_types=1);

namespace LM\Common\Model;

use Serializable;
use UnexpectedValueException;

/**
 * Immutable object that represents a string.
 */
class StringObject implements Serializable
{
    private $string;

    public function __construct(string $string)
    {
        $this->string = $string;
    }

    public function toString(): string
    {
        return $this->string;
    }

    public function serialize(): string
    {
        return serialize($this->string);
    }

    public function unserialize($serialized): void
    {
        $unserialized = unserialize($serialized);
        if (is_string($unserialized)) {
            $this->string = $unserialized;
        } else {
            throw new UnexpectedValueException();
        }
    }
}
