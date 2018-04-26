<?php

declare(strict_types=1);

namespace LM\Common\Enum;

use Serializable;

/**
 * Interface for enumerations.
 */
interface IEnum extends Serializable
{
    /**
     * @return string The value of the enumeration.
     */
    public function getValue(): string;

    /**
     * @param IEnum $value The enumeration to compare the object with.
     * @return bool Whether the two enumerations represent the same value.
     */
    public function is(IEnum $value): bool;
}
