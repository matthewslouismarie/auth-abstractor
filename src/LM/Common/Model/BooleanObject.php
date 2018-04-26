<?php

declare(strict_types=1);

namespace LM\Common\Model;

use Serializable;
use UnexpectedValueException;

/**
 * Immutable and serializable object that represents a boolean.
 */
class BooleanObject implements Serializable
{
    /** @var bool */
    private $boolean;

    /**
     * @param bool $boolean The boolean value of the object.
     */
    public function __construct(bool $boolean)
    {
        $this->boolean = $boolean;
    }

    /**
     * Converts the object into a scalar PHP boolean value.
     *
     * @return bool The boolean value of the object.
     */
    public function toBoolean(): bool
    {
        return $this->boolean;
    }

    public function serialize(): string
    {
        return serialize($this->boolean);
    }

    public function unserialize($serialized): void
    {
        $unserialized = unserialize($serialized);
        if (is_bool($unserialized)) {
            $this->boolean = $unserialized;
        } else {
            throw new UnexpectedValueException();
        }
    }
}
