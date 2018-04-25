<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Model;

use Psr\Http\Message\ResponseInterface;

/**
 * @todo Should distinguish between the first time and the subsequent times.
 */
interface IAuthenticationCallback
{
    public function handleSuccessfulProcess(AuthenticationProcess $authProcess): ResponseInterface;

    public function handleFailedProcess(AuthenticationProcess $authProcess): ResponseInterface;
}
