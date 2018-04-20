<?php

namespace LM\Authentifier\Implementation;

use Closure;
use Exception;
use LM\Authentifier\Model\AuthenticationProcess;
use LM\Authentifier\Model\AuthentifierResponse;
use LM\Authentifier\Model\IAuthenticationCallback;
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

    public function handleFailedProcess(AuthenticationProcess $authProcess): AuthentifierResponse
    {
        return ($this->failureClosure)($authProcess);
    }

    public function handleSuccessfulProcess(AuthenticationProcess $authProcess): AuthentifierResponse
    {
        return ($this->successClosure)($authProcess);
    }     
}
