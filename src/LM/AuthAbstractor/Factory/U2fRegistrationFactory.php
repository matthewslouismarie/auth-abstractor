<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Factory;

use Firehed\U2F\Registration;
use LM\AuthAbstractor\Implementation\U2fRegistration;
use LM\AuthAbstractor\Model\IU2fRegistration;

/**
 * This class provides methods Firehed U2F registrations into implementations of
 * IU2fRegistration, and vice versa.
 *
 * @todo Add unit tests.
 */
class U2fRegistrationFactory
{
    public function fromFirehed(Registration $registration): IU2fRegistration
    {
        return new U2fRegistration(
            base64_encode($registration->getAttestationCertificateBinary()),
            $registration->getCounter(),
            base64_encode($registration->getKeyHandleBinary()),
            base64_encode($registration->getPublicKey())
        )
        ;
    }

    public function toFirehed(IU2fRegistration $registration): Registration
    {
        return (new Registration())
            ->setAttestationCertificate(base64_decode($registration->getAttestationCertificate(), true))
            ->setCounter($registration->getCounter())
            ->setKeyHandle(base64_decode($registration->getKeyHandle(), true))
            ->setPublicKey(base64_decode($registration->getPublicKey(), true))
        ;
    }
}
