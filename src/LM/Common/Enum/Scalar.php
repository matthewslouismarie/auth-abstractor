<?php

declare(strict_types=1);

namespace LM\Common\Enum;

/**
 * Enumeration for PHP scalar types.
 */
class Scalar extends AbstractEnum
{
    /** @var string */
    const _STR = 'string';

    /** @var string */
    const _BOOL = 'boolean';

    /** @var string */
    const _INT = 'integer';

    /** @var string */
    const _ARRAY = 'array';
}
