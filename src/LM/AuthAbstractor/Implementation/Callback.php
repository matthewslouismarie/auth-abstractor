<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Implementation;

use Closure;
use LM\AuthAbstractor\Model\AuthenticationProcess;
use Psr\Http\Message\ResponseInterface;
use LM\AuthAbstractor\Model\IAuthenticationCallback;

/**
 * This is a convenience implementation of IAuthenticationCallback.
 *
 * If the callbacks you define are long, you might want to define your own
 * implementation of IAuthenticationCallback, for readability and reusability
 * purposes.
 *
 * @see \LM\AuthAbstractor\Model\IAuthenticationCallback
 * @todo Check closure signature?
 */
class Callback implements IAuthenticationCallback
{
    /** @var Closure */
    private $failureClosure;

    /** @var Closure */
    private $successClosure;

    /**
     * @api
     * @param Closure $failureClosure The closure to call if the authentication
     * process fails. It must accept an AuthenticationProcess as an argument,
     * and return a ResponseInterface.
     * @param Closure $successClosure The closure to call if the authentication
     * process succeeds. It must accept an AuthenticationProcess as an argument,
     * and return a ResponseInterface.
     * @todo Should actually accept IAuthenticationProcesses.
     */
    public function __construct(Closure $failureClosure, Closure $successClosure)
    {
        $this->failureClosure = $failureClosure;
        $this->successClosure = $successClosure;
    }

    public function handleFailedProcess(AuthenticationProcess $authProcess): ResponseInterface
    {
        return ($this->failureClosure)($authProcess);
    }

    public function handleSuccessfulProcess(AuthenticationProcess $authProcess): ResponseInterface
    {
        return ($this->successClosure)($authProcess);
    }
}
