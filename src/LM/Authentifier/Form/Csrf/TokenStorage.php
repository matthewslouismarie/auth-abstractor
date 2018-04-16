<?php

namespace LM\Authentifier\Form\Csrf;

use LM\Common\DataStructure\TypedMap;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class TokenStorage implements TokenStorageInterface
{
    private $token;

    public function getToken()
    {
        return $token;
    }

    /**
     * @todo Forces TypedData to be mutable, great…
    */
    public function setToken(TokenInterface $token = null)
    {
        $this->token = $token;
    }
}
