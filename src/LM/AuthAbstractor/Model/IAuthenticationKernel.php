<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Model;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface for the library middleware.
 */
interface IAuthenticationKernel
{
    public function processHttpRequest(
        ServerRequestInterface $httpRequest,
        AuthenticationProcess $process,
        IAuthenticationCallback $callback
    ): AuthentifierResponse;
}
