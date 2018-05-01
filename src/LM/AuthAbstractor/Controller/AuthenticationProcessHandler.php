<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use LM\AuthAbstractor\Model\IAuthenticationCallback;
use LM\AuthAbstractor\Model\IAuthenticationProcess;
use LM\AuthAbstractor\Model\IAuthentifierResponse;
use LM\AuthAbstractor\Implementation\AuthentifierResponse;

/**
 * This is a class used by AuthenticationKernel to handle requests.
 *
 * @internal
 */
class AuthenticationProcessHandler
{
    /** @var ContainerInterface */
    private $container;

    /**
     * @param ContainerInterface $container The container of auth-abtractor.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Used by the kernel to "apply" the current HTTP request on the current challenge.
     */
    public function handleAuthenticationProcess(
        ?ServerRequestInterface $httpRequest,
        IAuthenticationProcess $process,
        IAuthenticationCallback $callback
    ): IAuthentifierResponse {
        if ($process->isOngoing()) {
            $challenge = $this
                ->container
                ->get($process->getCurrentChallenge())
            ;
            $challengeResponse = $challenge->process($process, $httpRequest);

            $psrHttpResponse = $challengeResponse->getHttpResponse();

            if ($challengeResponse->isFinished()) {
                return new AuthentifierResponse(
                    $challengeResponse
                        ->getAuthenticationProcess()
                        ->resetNFailedAttempts()
                        ->setToNextChallenge(),
                    null
                );
            } elseif ($challengeResponse->isFailedAttempt()) {
                $updatedProcess = $challengeResponse
                    ->getAuthenticationProcess()
                    ->incrementNFailedAttempts()
                ;
                if ($updatedProcess->isFailed()) {
                    return new AuthentifierResponse(
                        $updatedProcess,
                        null
                    );
                } else {
                    return new AuthentifierResponse(
                        $updatedProcess,
                        $psrHttpResponse
                    );
                }
            } else {
                return new AuthentifierResponse(
                    $challengeResponse
                        ->getAuthenticationProcess(),
                    $psrHttpResponse
                );
            }
        } elseif ($process->isFailed()) {
            return new AuthentifierResponse(
                $process,
                $callback->handleFailedProcess($process)
            );
        } elseif ($process->isSucceeded()) {
            return new AuthentifierResponse(
                $process,
                $callback->handleSuccessfulProcess($process)
            );
        }
    }
}
