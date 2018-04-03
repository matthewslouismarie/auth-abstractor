<?php

namespace LM\Common\Enum;

interface IEnum
{
    public function getValue(): string;

    public function is(IEnum $enum): bool;
}
