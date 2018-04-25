<?php

namespace LM\AuthAbstractor\Implementation;

use Closure;
use Exception;
use LM\AuthAbstractor\Model\AuthenticationProcess;
use Psr\Http\Message\ResponseInterface;
use LM\AuthAbstractor\Model\IAuthenticationCallback;
use Psr\Container\ContainerInterface;

/**
 * @todo Check closure signature?
 */
class Callback implements IAuthenticationCallback
{
    private $failureClosure;

    private $successClosure;

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
