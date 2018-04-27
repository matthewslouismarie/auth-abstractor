<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Challenge;

use LM\AuthAbstractor\Model\AuthenticationProcess;
use LM\AuthAbstractor\Model\IChallengeResponse;
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
    /**
     * This method is called by the library on the current challenge.
     *
     * The kernel will call this method on the current challenge object of the
     * authentication process. Challenges are implementations of IChallenge and
     * are defined when first instantiating the authentication process. The
     * method will return an HTTP response, whether the HTTP request was a
     * submission and whether it failed, and a new authentication process. The
     * HTTP response will be returned to the application (which should normally
     * display it to the user).
     *
     * @link https://www.php-fig.org/psr/psr-7/
     * @param AuthenticationProcess $authenticationProcess The current
     * authentication process when the method starts.
     * @param null|ServerRequestInterface $httpRequest A PSR-7 representation
     * of an HTTP request.
     * @return ChallengeResponse A challenge response.
     * @todo Replace AuthenticationProcess and ChallengeResponse by
     * IAuthenticationProcess and IChallengeResponse.
     */
    public function process(
        AuthenticationProcess $authenticationProcess,
        ?ServerRequestInterface $httpRequest
    ): IChallengeResponse
    ;
}
