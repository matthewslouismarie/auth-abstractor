<?php

namespace LM\Authentifier\U2f;

use Firehed\U2F\Registration;
use Firehed\U2F\SignRequest;
use Firehed\U2F\SignResponse;
use LM\Authentifier\Exception\NoRegisteredU2fTokenException;
use LM\Common\Model\ArrayObject;

class U2fAuthenticationManager
{
    private $u2fServerGenerator;

    public function __construct(
        U2fServerGenerator $u2fServerGenerator)
    {
        $this->u2fServerGenerator = $u2fServerGenerator;
    }

    /**
     * @todo Type hint $registrations.
     * (It needs to be "id" => Firehed\U2F\Registration.)
     */
    public function generate(
        string $username,
        ArrayObject $registrations,
        array $idsToExclude = [])
    {
        $signRequests = $this
            ->u2fServerGenerator
            ->getServer()
            ->generateSignRequests($registrations->toArray(Registration::class))
        ;

        foreach ($idsToExclude as $id) {
            unset($signRequests[$id]);
        }

        if (0 === count($signRequests)) {
            throw new NoRegisteredU2fTokenException();
        }

        return $signRequests;
    }

    public function processResponse(
        ArrayObject $registrations,
        ArrayObject $signRequests,
        string $u2fTokenResponse): Registration
    {
        $server = $this
            ->u2fServerGenerator
            ->getServer()
        ;

        $server
            ->setRegistrations($registrations->toArray(Registration::class))
            ->setSignRequests($signRequests->toArray(SignRequest::class))
        ;
        $response = SignResponse::fromJson($u2fTokenResponse);
        $registration = $server->authenticate($response);

        return $registration;
    }

    private function getAuthenticatorId(
        array $sign_requests,
        string $challenge): string
    {
        foreach ($sign_requests as $authenticator_id => $sign_request) {
            if ($sign_request->getChallenge() === $challenge) {
                return $authenticator_id;
            }
        }
    }
}
