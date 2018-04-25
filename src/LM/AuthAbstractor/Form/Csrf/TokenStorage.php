<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Form\Csrf;

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
