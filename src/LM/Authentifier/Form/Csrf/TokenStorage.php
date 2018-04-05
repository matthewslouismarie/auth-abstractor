<?php

namespace LM\Authentifier\Form\Csrf;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TokenStorage implements TokenStorageInterface
{
    private $token;

    public function getToken()
    {
        return $token;
    }

    /**
     * @todo Forces DataManager to be mutable, great…
    */
    public function setToken(TokenInterface $token = null)
    {
        $this->token = $token;
    }
}
