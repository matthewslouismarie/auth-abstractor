<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Challenge;

use LM\AuthAbstractor\Model\AuthenticationProcess;
use LM\AuthAbstractor\Model\IChallengeResponse;
use LM\AuthAbstractor\Model\IChallengeResponse;
use Psr\Http\Message\ServerRequestInterface;

class EmptyChallenge implements IChallenge
{
    public function process(
        AuthenticationProcess $authenticationProcess,
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
