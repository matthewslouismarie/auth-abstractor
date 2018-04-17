<?php

namespace LM\Authentifier\Factory;

use Firehed\U2F\Registration;
use LM\Authentifier\Implementation\U2fRegistration;
use LM\Authentifier\Model\IU2fRegistration;

/**
 * @todo Unit tests
 */
class U2fRegistrationFactory
{
    public function fromFirehed(Registration $registration): IU2fRegistration
    {
        return new U2fRegistration(
            $registration->getAttestationCertificateBinary(),
            $registration->getCounter(),
            $registration->getKeyHandleBinary(),
            $registration->getPublicKey()
        )
        ;
    }

    public function toFirehed(IU2fRegistration $registration): Registration
    {
        return (new Registration())
            ->setAttestationCertificate($registration->getAttestationCertificateBinary())
            ->setCounter($registration->getCounter())
            ->setKeyHandle($registration->getKeyHandleBinary())
            ->setPublicKey($registration->getPublicKeyBinary())
        ;
    }
}
