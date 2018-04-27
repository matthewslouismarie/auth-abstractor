<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Controller;

use LM\AuthAbstractor\Configuration\IApplicationConfiguration;
use LM\AuthAbstractor\Model\AuthenticationProcess;
use LM\AuthAbstractor\Model\AuthentifierResponse;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use LM\AuthAbstractor\Model\IAuthenticationCallback;

/**
 * This is a class used by AuthenticationKernel to handle requests.
 *
 * @internal
 */
class AuthenticationProcessHandler
{
    /** @var IApplicationConfiguration */
    private $appConfig;

    /** @var ContainerInterface */
    private $container;

    /**
     * @param IApplicationConfiguration $appConfig The configuration of the
     * application.
     */
    public function __construct(
        IApplicationConfiguration $appConfig,
        ContainerInterface $container
    ) {
        $this->appConfig = $appConfig;
        $this->container = $container;
    }

    /**
     * Used by the kernel to "apply" the current HTTP request on the current challenge.
     * @todo Remove callback from authentication process object.
     */
    public function handleAuthenticationProcess(
        ?ServerRequestInterface $httpRequest,
        AuthenticationProcess $process,
        IAuthenticationCallback $callback
    ): AuthentifierResponse {
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
