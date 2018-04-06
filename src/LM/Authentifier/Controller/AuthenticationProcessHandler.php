<?php

namespace LM\Authentifier\Controller;

use LM\Authentifier\Configuration\IApplicationConfiguration;
use LM\Authentifier\Model\AuthenticationProcess;
use LM\Authentifier\Model\AuthentifierResponse;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;

class AuthenticationProcessHandler
{
    private $appConfig;

    private $container;

    public function __construct(
        IApplicationConfiguration $appConfig,
        ContainerInterface $container)
    {
        $this->appConfig = $appConfig;
        $this->container = $container;
    }

    /**
     * @todo Check type of $challengeResponse.
     */
    public function handleAuthenticationProcess(
        RequestInterface $httpRequest,
        AuthenticationProcess $process): AuthentifierResponse
    {
        if ($process->isOngoing()) {
            $challenge = $this
                ->container
                ->get($process->getCurrentChallenge())
            ;
            $challengeResponse = $challenge->process($process, $httpRequest);

            $psr7Factory = new DiactorosFactory();
            $psrHttpResponse = $psr7Factory->createResponse($challengeResponse->getHttpResponse());

            if ($challengeResponse->isFinished()) {
                return new AuthentifierResponse(
                    $challengeResponse
                        ->getAuthenticationProcess()
                        ->resetNFailedAttempts()
                        ->setToNextChallenge(),
                    null)
                ;
            } elseif ($challengeResponse->isFailedAttempt()) {
                return new AuthentifierResponse(
                    $challengeResponse
                        ->getAuthenticationProcess()
                        ->incrementNFailedAttempts(),
                    $psrHttpResponse)
                ;
            } else {
                return new AuthentifierResponse(
                    $challengeResponse
                        ->getAuthenticationProcess(),
                    $psrHttpResponse)
                ;
            }
        } elseif ($process->IsFailed()) {
            $callback = $process->getCallback();
            $callback->wakeUp($this
                    ->appConfig
                    ->getContainer())
            ;

            return $callback->filterFailureResponse($process, new Response(""));
        } elseif ($process->isSucceeded()) {
            $callback = $process->getCallback();
            $callback->wakeUp($this
                    ->appConfig
                    ->getContainer())
            ;

            return $callback->filterSuccessResponse($process, new Response(""));
            
        }
    }
}
