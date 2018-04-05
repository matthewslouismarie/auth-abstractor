<?php

namespace LM\Authentifier\Form\Csrf;

interface TokenStorageInterface
{
    public function getToken();

    public function setToken($token);
}
