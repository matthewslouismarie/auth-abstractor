<?php

namespace LM\Authentifier\Model;

use Serializable;

/**
 * @todo Properties must be string.
 */
interface IDataHolder extends Serializable
{
    public function get(string $property);

    public function getObject(string $property, string $class);

    /**
     * @todo Shouldn't probably be here. Either static method or object that
     * would be given once to RequestDatum when created.
     */
    public function getProperties(): array;
}
