<?php

declare(strict_types=1);

namespace LM\Authentifier\Model;

use Psr\Container\ContainerInterface;
use Serializable;

/**
 * @todo Should distinguish between the first time and the subsequent times.
 * @todo Remove Serializable.
 * @todo Remove wakeUp().
 */
interface IAuthenticationCallback extends Serializable
{
    public function handleSuccessfulProcess(AuthenticationProcess $authProcess): AuthentifierResponse;

    public function handleFailedProcess(AuthenticationProcess $authProcess): AuthentifierResponse;

    public function wakeUp(ContainerInterface $container): void;
}
