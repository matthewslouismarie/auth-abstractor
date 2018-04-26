<?php

declare(strict_types=1);

namespace LM\Common\DataStructure;

/**
 * This interface defines immutable maps that check the type of objects added
 * and removed from it.
 *
 * @deprecated
 * @internal
 * @todo The type should be set at construction time and left unchanged and
 * unspecified after.
 */
interface ITypedMap
{
    public function get(string $key, string $type);

    public function getSize(): int;

    public function set(string $key, $value, string $type): ITypedMap;

    public function add(string $key, $value, string $type): ITypedMap;

    public function toArray(): array;
}
