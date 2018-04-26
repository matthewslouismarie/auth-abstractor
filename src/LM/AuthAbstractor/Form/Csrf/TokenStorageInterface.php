<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Form\Csrf;

/**
 * @deprecated
 * @todo Delete.
 */
interface TokenStorageInterface
{
    /** @ignore */
    public function getToken();

    /** @ignore */
    public function setToken($token);
}
