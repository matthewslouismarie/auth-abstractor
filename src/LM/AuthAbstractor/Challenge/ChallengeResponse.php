<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Challenge;

use Symfony\Component\HttpFoundation\Response;
use LM\AuthAbstractor\Model\AuthenticationProcess;

/**
 * The only use of this class is to be returned by challenges.
 *
 * They contain the HTTP response, the new authentication process, and whether
 * the request was a failed attempt and is finished. For instance, a challenge
 * can return after having checked that the user entered a valid password:
 *
 *     return new ChallengeResponse(
 *         $authProcess, // the updated authentication process
 *         null, // the HTTP response
 *         true, // whether the request was a submission
 *         true // whether the submission was valid (e.g. a valid password)
 *     );
 *
 * @todo It should implement an interface.
 */
class ChallengeResponse
{
    private $authenticationProcess;

    private $httpResponse;

    private $isFailedAttempt;

    private $isFinished;

    /**
     * @param AuthenticationProcess $authenticationProcess The authentication
     * process.
     * @param null|Response $httpResponse The HTTP response.
     * @param bool $isFailedAttempt Whether the HTTP request was a failed
     * submission.
     * @param bool $isFinished Whether the current challenge is finished.
     */
    public function __construct(
        AuthenticationProcess $authenticationProcess,
        ?Response $httpResponse,
        bool $isFailedAttempt,
        bool $isFinished
    ) {
        $this->authenticationProcess = $authenticationProcess;
        $this->httpResponse = $httpResponse;
        $this->isFailedAttempt = $isFailedAttempt;
        $this->isFinished = $isFinished;
    }

    /**
     * @return AuthenticationProcess The authentication process.
     * @todo It should return an IAuthenticationProcess instead.
     */
    public function getAuthenticationProcess(): AuthenticationProcess
    {
        return $this->authenticationProcess;
    }

    /**
     * @return Response The HTTP response.
     * @todo It should return a ResponseInterface instead.
     */
    public function getHttpResponse(): ?Response
    {
        return $this->httpResponse;
    }

    /**
     * @return bool Whether the HTTP request was a failed attempt.
     */
    public function isFailedAttempt(): bool
    {
        return $this->isFailedAttempt;
    }

    /**
     * @return bool Whether the current challenge is finished or not.
     */
    public function isFinished(): bool
    {
        return $this->isFinished;
    }
}
