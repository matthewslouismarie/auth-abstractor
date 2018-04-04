<?php

namespace LM\Authentifier\Model;

use LM\Authentifier\Enum\Persistence\Operation;

use Serializable;

class PersistOperation implements Serializable
{
    private $object;

    private $operation;

    public function __construct(Serializable $object, Operation $operation)
    {
        $this->object = $object;
        $this->operation = $operation;
    }

    public function getType(): Operation
    {
        return $this->operation;
    }

    public function getObject(): Serializable
    {
        return $this->object;
    }

    public function serialize()
    {
        return serialize([
            $this->object,
            $this->operation,
        ]);
    }

    public function unserialize($serialized)
    {
        list(
            $this->object,
            $this->operation) = unserialize($serialized);
    }
}
