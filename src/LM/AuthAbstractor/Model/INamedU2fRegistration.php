<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Model;

/**
 * A U2F registration with a name.
 */
interface INamedU2fRegistration extends IU2fRegistration
{
    /**
     * @return string The name of the U2F registration.
     */
    public function getName(): string;
}
