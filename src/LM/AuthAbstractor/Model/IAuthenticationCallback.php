<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Model;

use Psr\Http\Message\ResponseInterface;

/**
 * An interface representing a callback called when an authentication process
 * fails or succeeds.
 *
 * @todo Should distinguish between the first time and the subsequent times.
 */
interface IAuthenticationCallback
{
    public function handleSuccessfulProcess(AuthenticationProcess $authProcess): ResponseInterface;

    public function handleFailedProcess(AuthenticationProcess $authProcess): ResponseInterface;
}
