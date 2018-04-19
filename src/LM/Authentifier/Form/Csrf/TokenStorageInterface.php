<?php

namespace LM\Authentifier\Form\Csrf;

/**
 * @todo Delete?
 */
interface TokenStorageInterface
{
    public function getToken();

    public function setToken($token);
}
