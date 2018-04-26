<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Enum\Persistence;

use LM\Common\Enum\AbstractEnum;

/**
 * This is an enum representing the type of a persist operation.
 *
 * @see \LM\AuthAbstractor\Enum\PersistOperation
 */
class Operation extends AbstractEnum
{
    const CREATE = "CREATE";

    const DELETE = "DELETE";

    const UPDATE = "UPDATE";
}
