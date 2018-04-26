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
    /**
     * This method must be called when the authentication process succeeds.
     *
     * @param AuthenticationProcess $authProcess The succeeded authentication
     * process.
     * @return ResponseInterface The HTTP response which must then be returned
     * by the kernel.
     */
    public function handleSuccessfulProcess(AuthenticationProcess $authProcess): ResponseInterface;

    /**
     * This method must be called when the authentication process fails.
     *
     * @param AuthenticationProcess $authProcess The failed authentication
     * process.
     * @return ResponseInterface The HTTP response which must then be returned
     * by the kernel.
     */
    public function handleFailedProcess(AuthenticationProcess $authProcess): ResponseInterface;
}
