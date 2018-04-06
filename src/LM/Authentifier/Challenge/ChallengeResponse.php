<?php

namespace LM\Authentifier\Challenge;

use Symfony\Component\HttpFoundation\Response;
use LM\Authentifier\Enum\AuthenticationProcess\Status;
use LM\Authentifier\Model\AuthenticationProcess;

class ChallengeResponse
{
    private $authenticationProcess;

    private $httpResponse;

    private $isFailedAttempt;

    private $isFinished;

    public function __construct(
        AuthenticationProcess $authenticationProcess,
        ?Response $httpResponse,
        bool $isFailedAttempt,
        bool $isFinished)
    {
        $this->authenticationProcess = $authenticationProcess;
        $this->httpResponse = $httpResponse;
        $this->isFailedAttempt = $isFailedAttempt;
        $this->isFinished = $isFinished;
    }

    public function getAuthenticationProcess(): AuthenticationProcess
    {
        return $this->authenticationProcess;
    }

    public function getHttpResponse(): ?Response
    {
        return $this->httpResponse;
    }

    public function isFailedAttempt(): bool
    {
        return $this->isFailedAttempt;
    }

    public function isFinished(): bool
    {
        return $this->isFinished;
    }
}
