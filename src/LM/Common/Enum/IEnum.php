<?php

declare(strict_types=1);

namespace LM\Common\Enum;

use Serializable;

interface IEnum extends Serializable
{
    public function getValue(): string;

    public function is(IEnum $value): bool;
}
