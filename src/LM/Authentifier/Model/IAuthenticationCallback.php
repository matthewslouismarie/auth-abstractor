<?php

namespace LM\Authentifier\Model;

use Psr\Container\ContainerInterface;
use Serializable;

/**
 * @todo Should distinguish between the first time and the subsequent times.
 */
interface IAuthenticationCallback extends Serializable
{
    public function handleSuccessfulProcess(AuthenticationProcess $authProcess): AuthentifierResponse;

    public function handleFailedProcess(AuthenticationProcess $authProcess): AuthentifierResponse;

    public function wakeUp(ContainerInterface $container): void;
}
