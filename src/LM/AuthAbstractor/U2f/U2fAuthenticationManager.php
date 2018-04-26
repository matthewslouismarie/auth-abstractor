<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\U2f;

use Firehed\U2F\SignRequest;
use Firehed\U2F\SignResponse;
use LM\AuthAbstractor\Exception\NoRegisteredU2fTokenException;
use LM\AuthAbstractor\Factory\U2fRegistrationFactory;
use LM\Common\Model\ArrayObject;
use LM\AuthAbstractor\Model\IU2fRegistration;

/**
 * This class is used for generating U2F sign requests, and for processing their
 * responses.
 */
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
