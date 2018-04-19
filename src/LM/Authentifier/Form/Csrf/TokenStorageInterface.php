<?php

declare(strict_types=1);

namespace LM\Authentifier\Form\Csrf;

/**
 * @todo Delete?
 */
interface TokenStorageInterface
{
    public function getToken();

    public function setToken($token);
}
