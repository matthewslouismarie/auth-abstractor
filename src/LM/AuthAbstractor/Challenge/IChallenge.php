<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Challenge;

use LM\AuthAbstractor\Model\AuthenticationProcess;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Classes implementing this interface are challenges.
 *
 * Challenges represent steps of the authentication process. For example, there
 * can be one challenge for asking the user for their username and password, and
 * another one that asks the user to confirm their identity with their U2F
 * security key. Challenges are specified when creating the authentication
 * process, and the order in which they are specified matters.
 *
 * @see \LM\AuthAbstractor\Factory\AuthenticationProcessFactory
 * @todo Should be renamed: U2fRegistrationChallenge is not a challenge for
 * instance.
 * @todo Should only mention IAuthenticationProcess and use an interface for
 * ChallengeResponse.
 */
interface IChallenge
{
    public function process(
        AuthenticationProcess $authenticationProcess,
        ?ServerRequestInterface $httpRequest
    ): ChallengeResponse
    ;
}
