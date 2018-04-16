<?php

namespace LM\Authentifier\Factory;

use Firehed\U2f\Registration;
use LM\Authentifier\Implementation\U2fRegistration;
use LM\Authentifier\Model\IU2fRegistration;

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

    public function toFirehed()
    {
    }
}
