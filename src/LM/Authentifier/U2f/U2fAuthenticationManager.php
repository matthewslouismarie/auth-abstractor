<?php

namespace LM\Authentifier\U2f;

use Firehed\U2F\Registration;
use Firehed\U2F\SignRequest;
use Firehed\U2F\SignResponse;
use LM\Authentifier\Exception\NoRegisteredU2fTokenException;
use LM\Authentifier\Factory\U2fRegistrationFactory;
use LM\Authentifier\Implementation\U2fRegistration;
use LM\Common\Model\ArrayObject;
use LM\Authentifier\Model\IU2fRegistration;

class U2fAuthenticationManager
{
    private $u2fRegistrationFactory;

    private $u2fServerGenerator;

    public function __construct(
        U2fRegistrationFactory $u2fRegistrationFactory,
        U2fServerGenerator $u2fServerGenerator
    ) {
        $this->u2fRegistrationFactory = $u2fRegistrationFactory;
        $this->u2fServerGenerator = $u2fServerGenerator;
    }

    /**
     * @todo Type hint $registrations.
     */
    public function generate(
        string $username,
        ArrayObject $registrations
    ): array {
        $firehedRegs = array_map(
            [$this->u2fRegistrationFactory, 'toFirehed'],
            $registrations->toArray(IU2fRegistration::class)
        );
        $signRequests = $this
            ->u2fServerGenerator
            ->getServer()
            ->generateSignRequests($firehedRegs)
        ;

        if (0 === count($signRequests)) {
            throw new NoRegisteredU2fTokenException();
        }

        return $signRequests;
    }

    public function processResponse(
        ArrayObject $registrations,
        ArrayObject $signRequests,
        string $u2fTokenResponse
    ): IU2fRegistration {
        $server = $this
            ->u2fServerGenerator
            ->getServer()
        ;
        $firehedRegs = array_map(
            [$this->u2fRegistrationFactory, 'toFirehed'],
            $registrations->toArray(IU2fRegistration::class)
        );
        $server
            ->setRegistrations($firehedRegs)
            ->setSignRequests($signRequests->toArray(SignRequest::class))
        ;
        $response = SignResponse::fromJson($u2fTokenResponse);
        $registration = $server->authenticate($response);

        return $this
            ->u2fRegistrationFactory
            ->fromFirehed($registration)
        ;
    }
}
