<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Challenge;

use LM\AuthAbstractor\Model\AuthenticationProcess;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @todo Should be renamed to RequestHandler or something…
 */
interface IChallenge
{
    public function process(
        AuthenticationProcess $authenticationProcess,
        ?ServerRequestInterface $httpRequest
    ): ChallengeResponse
    ;
}
