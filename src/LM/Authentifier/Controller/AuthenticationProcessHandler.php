<?php

declare(strict_types=1);

namespace LM\Authentifier\Controller;

use LM\Authentifier\Configuration\IApplicationConfiguration;
use LM\Authentifier\Model\AuthenticationProcess;
use LM\Authentifier\Model\AuthentifierResponse;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use LM\Authentifier\Model\IAuthenticationCallback;

class AuthenticationProcessHandler
{
    private $appConfig;

    private $container;

    public function __construct(
        IApplicationConfiguration $appConfig,
        ContainerInterface $container
    ) {
        $this->appConfig = $appConfig;
        $this->container = $container;
    }

    /**
     * @todo Remove callback from authentication process object.
     */
    public function handleAuthenticationProcess(
        ?ServerRequestInterface $httpRequest,
        AuthenticationProcess $process
    ): AuthentifierResponse {
        if ($process->isOngoing()) {
            $challenge = $this
                ->container
                ->get($process->getCurrentChallenge())
            ;
            $challengeResponse = $challenge->process($process, $httpRequest);

            $psr7Factory = new DiactorosFactory();

            $psrHttpResponse = null;
            if (null !== $challengeResponse->getHttpResponse()) {
                $psrHttpResponse = $psr7Factory->createResponse($challengeResponse->getHttpResponse());
            }

            if ($challengeResponse->isFinished()) {
                return new AuthentifierResponse(
                    $challengeResponse
                        ->getAuthenticationProcess()
                        ->resetNFailedAttempts()
                        ->setToNextChallenge(),
                    null
                )
                ;
            } elseif ($challengeResponse->isFailedAttempt()) {
                $updatedProcess = $challengeResponse
                    ->getAuthenticationProcess()
                    ->incrementNFailedAttempts()
                ;
                if ($updatedProcess->isFailed()) {
                    return new AuthentifierResponse(
                        $updatedProcess,
                        null
                    )
                    ;
                } else {
                    return new AuthentifierResponse(
                        $updatedProcess,
                        $psrHttpResponse
                    )
                    ;
                }
            } else {
                return new AuthentifierResponse(
                    $challengeResponse
                        ->getAuthenticationProcess(),
                    $psrHttpResponse
                )
                ;
            }
        } elseif ($process->isFailed()) {
            return $callback->handleFailedProcess($process);
        } elseif ($process->isSucceeded()) {
            return $callback->handleSuccessfulProcess($process);
        }
    }
}
