<?php

declare(strict_types=1);

namespace LM\Common\Enum;

use Serializable;

/**
 * Interface for enumerations.
 */
interface IEnum extends Serializable
{
    public function getValue(): string;

    public function is(IEnum $value): bool;
}
