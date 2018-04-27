<?php

declare(strict_types=1);

namespace LM\Common\DataStructure;

use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use Serializable;

/**
 * Interface for immutable list objects.
 *
 * Implementations of this class MUST not allow the object to be modified.
 * Instead,
 * @todo Make it extend an IComparable interface.
 * @todo Check for conventions (book about design patterns).
 */
interface IImmutableList extends Countable, IteratorAggregate, Serializable
{
    /**
     * @param $item The item to add to the end of the list.
     * @throws InvalidArgumentException if $item is not of the list's type.
     */
    public function append($item): IImmutableList;
}
