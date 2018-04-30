<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Implementation;

use Symfony\Component\HttpFoundation\Response;
use LM\AuthAbstractor\Model\IAuthenticationProcess;
use Psr\Http\Message\ResponseInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use LM\AuthAbstractor\Model\IChallengeResponse;

/**
 * The only use of this class is to be returned by challenges. It accepts
 * Symfony responses objects but return ResponseInterface objects.
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
 * @todo Move in Implementation
 */
class ChallengeResponse implements IChallengeResponse
{
    /** @var IAuthenticationProcess */
    private $authenticationProcess;

    /** @var null|Response */
    private $httpResponse;

    /** @var bool */
    private $isFailedAttempt;

    /** @var bool */
    private $isFinished;

    /**
     * @param IAuthenticationProcess $authenticationProcess The authentication
     * process.
     * @param null|Response $httpResponse The HTTP response.
     * @param bool $isFailedAttempt Whether the HTTP request was a failed
     * submission.
     * @param bool $isFinished Whether the current challenge is finished.
     */
    public function __construct(
        IAuthenticationProcess $authenticationProcess,
        ?Response $httpResponse,
        bool $isFailedAttempt,
        bool $isFinished
    ) {
        $this->authenticationProcess = $authenticationProcess;
        $this->httpResponse = $httpResponse;
        $this->isFailedAttempt = $isFailedAttempt;
        $this->isFinished = $isFinished;
    }

    public function getAuthenticationProcess(): IAuthenticationProcess
    {
        return $this->authenticationProcess;
    }

    public function getHttpResponse(): ?ResponseInterface
    {
        if (null === $this->httpResponse) {
            return null;
        } else {
            $diactorosFactory = new DiactorosFactory();

            return $diactorosFactory->createResponse($this->httpResponse);
        }
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
