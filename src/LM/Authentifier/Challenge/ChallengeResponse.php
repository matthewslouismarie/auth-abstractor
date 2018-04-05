<?php

namespace LM\Authentifier\Challenge;

use Symfony\Component\HttpFoundation\Response;
use LM\Authentifier\Enum\AuthenticationProcess\Status;
use LM\Authentifier\Model\AuthenticationProcess;

class ChallengeResponse
{
    private $authenticationProcess;

    private $httpResponse;

    private $isAttempt;

    private $isSuccessful;

    public function __construct(
        AuthenticationProcess $authenticationProcess,
        Response $httpResponse,
        bool $isAttempt,
        bool $isSuccessful)
    {
        $this->authenticationProcess = $authenticationProcess;
        $this->httpResponse = $httpResponse;
        $this->isAttempt = $isAttempt;
        $this->isSuccessful = $isSuccessful;
    }

    public function getAuthenticationProcess(): AuthenticationProcess
    {
        return $this->authenticationProcess;
    }

    public function getHttpResponse(): Response
    {
        return $this->httpResponse;
    }

    public function isAttempt(): bool
    {
        return $this->isAttempt;
    }

    public function isSuccessful(): bool
    {
        return $this->isSuccessful;
    }
}
