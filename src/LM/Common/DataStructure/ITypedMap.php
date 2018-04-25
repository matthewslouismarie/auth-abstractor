<?php

declare(strict_types=1);

namespace LM\Common\DataStructure;

interface ITypedMap
{
    public function get(string $key, string $type);

    public function getSize(): int;

    public function set(string $key, $value, string $type): ITypedMap;

    public function add(string $key, $value, string $type): ITypedMap;

    public function toArray(): array;
}
