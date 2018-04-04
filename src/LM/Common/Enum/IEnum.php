<?php

namespace LM\Common\Enum;

use Serializable;

interface IEnum extends Serializable
{
    public function getValue(): string;

    public function is(IEnum $value): bool;
}
