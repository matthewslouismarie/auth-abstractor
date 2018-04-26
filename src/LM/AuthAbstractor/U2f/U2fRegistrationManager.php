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
 *
 * @internal
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

    /**
     * Generates a new U2F register requset.
     *
     * @param null|ArrayObject $registrations An array of IU2fRegistration.
     * @todo Use an ArrayObject instead?
     */
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

    /**
     * Verifies and returns a new U2F registration from a response.
     *
     * @param string $u2fKeyResponse The response from the U2F token.
     * @param RegisterRequest $request The U2F register request.
     * @return IU2fRegistration The new U2F registration.
     * @todo Rename $u2fKeyResponse to $u2fTokenResponse
     */
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
