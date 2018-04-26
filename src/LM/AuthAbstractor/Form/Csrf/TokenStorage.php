<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Form\Csrf;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @deprecated
 * @todo Delete.
 */
class TokenStorage implements TokenStorageInterface
{
    /** @ignore */
    private $token;

    /** @ignore */
    public function getToken()
    {
        return $token;
    }

    /**
     * @ignore
     */
    public function setToken(TokenInterface $token = null)
    {
        $this->token = $token;
    }
}
