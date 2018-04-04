<?php

namespace LM\Common\Model;

use Serializable;
use UnexpectedValueException;

class BooleanObject implements Serializable
{
    private $boolean;

    public function __construct(bool $boolean)
    {
        $this->boolean = $boolean;
    }

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
