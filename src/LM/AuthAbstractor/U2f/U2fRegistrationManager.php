<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\U2f;

use LM\AuthAbstractor\Factory\U2fRegistrationFactory;
use LM\AuthAbstractor\Model\IU2fRegistration;
use LM\AuthAbstractor\Model\U2fRegistrationRequest;
use LM\Common\Model\ArrayObject;
use Firehed\U2F\RegisterRequest;
use Firehed\U2F\RegisterResponse;
use Firehed\U2F\SignRequest;

/**
 * This class is used for generating U2F register requests and processing their
 * responses.
 */
class U2fRegistrationManager
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

    public function generate(?ArrayObject $registrations = null): U2fRegistrationRequest
    {
        $server = $this
            ->u2fServerGenerator
            ->getServer()
        ;
        $request = $server->generateRegisterRequest();

        $signRequests = null;
        if (null !== $registrations) {
            $firehedRegs = array_map(
                [$this->u2fRegistrationFactory, 'toFirehed'],
                $registrations->toArray(IU2fRegistration::class)
            );
            $signRequests = new ArrayObject(
                $server->generateSignRequests($firehedRegs),
                SignRequest::class
            );
        }

        return new U2fRegistrationRequest($request, $signRequests);
    }

    public function getU2fRegistrationFromResponse(
        string $u2fKeyResponse,
        RegisterRequest $request
    ): IU2fRegistration {
        $server = $this
            ->u2fServerGenerator
            ->getServer()
        ;
        $server
            ->setRegisterRequest($request)
        ;
        $response = RegisterResponse::fromJson($u2fKeyResponse);
        $registration = $server->register($response);

        return $this
            ->u2fRegistrationFactory
            ->fromFirehed($registration)
        ;
    }
}
