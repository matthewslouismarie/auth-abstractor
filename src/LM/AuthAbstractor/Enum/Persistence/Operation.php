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
    /** @var string */
    const CREATE = "CREATE";

    /** @var string */
    const DELETE = "DELETE";

    /** @var string */
    const UPDATE = "UPDATE";
}
