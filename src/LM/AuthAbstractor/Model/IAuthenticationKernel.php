<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Model;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface for the library middleware.
 * @todo Make it PSR-15.
 */
interface IAuthenticationKernel
{
    /**
     * Process an HTTP request.
     *
     * This method should be called by the application's HTTP controller after
     * having initialised a new authentication process or retrieved the
     * current authentication process (e.g. from a PHP session).
     *
     * @param ServerRequestInterface $httpRequest The HTTP request.
     * @param AuthenticationProcess $process The current authentication process.
     * @param IAuthenticationCallback $callback A callback to execute if the
     * authentication process fails or succeeds (e.g. if the user manages to
     * prove their identity or not). You do not have to roll your own
     * IAuthenticationCallback implementation and can just instantiate Callback.
     * @return AuthentifierResponse An object containing an HTTP response that
     * should be sent back to the user, and a new authentication process that
     * should be stored to be retrieved later (e.g. in session).
     * @see \LM\AuthAbstractor\Implementation\Callback
     * @todo Make it PSR-15 oompliant.
     * @todo Should accept an IAuthenticationProcess parameter instead.
     */
    public function processHttpRequest(
        ServerRequestInterface $httpRequest,
        AuthenticationProcess $process,
        IAuthenticationCallback $callback
    ): AuthentifierResponse;
}
