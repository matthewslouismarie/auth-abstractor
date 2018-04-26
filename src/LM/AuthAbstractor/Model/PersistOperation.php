<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Model;

use LM\AuthAbstractor\Enum\Persistence\Operation;

use Serializable;

/**
 * PersistOperation objects represent actions that need to be persisted by the
 * application in some ways (e.g. in a database) when the authentication process
 * finishes (i.e. succeeds or fails).
 *
 * @todo Use an interface?
 */
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
