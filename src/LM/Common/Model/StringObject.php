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
    /** @var string */
    private $string;

    /**
     * @param string $string The value to initialise the object with.
     */
    public function __construct(string $string)
    {
        $this->string = $string;
    }

    /**
     * @return string A string representation of the object.
     */
    public function toString(): string
    {
        return $this->string;
    }

    /**
     * @deprecated
     */
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
