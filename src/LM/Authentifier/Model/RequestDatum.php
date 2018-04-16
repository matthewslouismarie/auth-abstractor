<?php

namespace LM\Authentifier\Model;

use UnexpectedValueException;
use Serializable;

class RequestDatum implements IDataHolder
{
    const CLASS_PROPERTY = "CLASS";

    const KEY_PROPERTY = "KEY";

    const VALUE_PROPERTY = "VALUE";

    private $values;

    public function __construct(
        string $key,
        Serializable $value)
    {
        $this->values = [
            self::CLASS_PROPERTY => get_class($value),
            self::KEY_PROPERTY => $key,
            self::VALUE_PROPERTY => $value,
        ];
    }

    public function get(string $property)
    {
        return $this->values[$property];
    }

    public function getObject(string $property, string $class)
    {
        $item = $this->values[$property];
        if (!is_a($item, $class)) {
            throw new UnexpectedValueException();
        }

        return $item;
    }

    /**
     * @todo Move in a new class AbstractRequestDatum for instance.
     */
    public function getProperties(): array
    {
        return array_key($this, null, true);
    }

    public function serialize()
    {
        return serialize($this->values);
    }

    public function unserialize($serialized)
    {
        $this->values = unserialize($serialized);
    }
}
