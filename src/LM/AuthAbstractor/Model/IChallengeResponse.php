<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Model;

use Psr\Http\Message\ResponseInterface;

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
 */
interface IChallengeResponse
{
    /**
     * @api
     * @return IAuthenticationProcess The authentication process.
     */
    public function getAuthenticationProcess(): IAuthenticationProcess;

    /**
     * @api
     * @return ResponseInterface The HTTP response.
     */
    public function getHttpResponse(): ?ResponseInterface;

    /**
     * @api
     * @return bool Whether the HTTP request was a failed attempt.
     */
    public function isFailedAttempt(): bool;

    /**
     * @api
     * @return bool Whether the current challenge is finished or not.
     */
    public function isFinished(): bool;
}
