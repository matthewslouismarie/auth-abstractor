<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Challenge;

use LM\AuthAbstractor\Model\IAuthenticationProcess;
use LM\AuthAbstractor\Model\IChallengeResponse;
use Psr\Http\Message\ServerRequestInterface;

class EmptyChallenge implements IChallenge
{
    public function process(
        IAuthenticationProcess $authenticationProcess,
        ?ServerRequestInterface $httpRequest
    ): IChallengeResponse {
        return new ChallengeResponse(
            $authenticationProcess,
            null,
            false,
            true
        );
    }
}
