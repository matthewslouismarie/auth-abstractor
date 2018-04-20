<?php

declare(strict_types=1);

namespace LM\Authentifier\Model;

/**
 * @todo Should distinguish between the first time and the subsequent times.
 */
interface IAuthenticationCallback
{
    public function handleSuccessfulProcess(AuthenticationProcess $authProcess): AuthentifierResponse;

    public function handleFailedProcess(AuthenticationProcess $authProcess): AuthentifierResponse;
}
