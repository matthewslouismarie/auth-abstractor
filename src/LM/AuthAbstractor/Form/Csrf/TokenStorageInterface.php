<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Form\Csrf;

/**
 * @deprecated
 * @todo Delete.
 */
interface TokenStorageInterface
{
    public function getToken();

    public function setToken($token);
}
