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

    /**
     * @param Serializable $object The entity that needs to be persisted.
     * @param Operation $operation The type of persistence (update, delete, or
     * create).
     */
    public function __construct(Serializable $object, Operation $operation)
    {
        $this->object = $object;
        $this->operation = $operation;
    }

    /**
     * @return Operation The type of persistence.
     */
    public function getType(): Operation
    {
        return $this->operation;
    }

    /**
     * @return Serializable The entity to persist.
     */
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
