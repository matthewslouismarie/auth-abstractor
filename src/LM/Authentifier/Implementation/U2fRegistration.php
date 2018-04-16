<?php

namespace LM\Authentifier\Implementation;

use LM\Authentifier\Model\IU2fRegistration;

class U2fRegistration implements IU2fRegistration
{
    private $attestationCertificateBinary;

    private $counter;

    private $keyHandleBinary;

    private $publicKeyBinary;

    public function __construct(
        string $attestationCertificateBinary,
        int $counter,
        string $keyHandleBinary,
        string $publicKeyBinary
    ) {
        $this->attestationCertificateBinary = $attestationCertificateBinary;
        $this->counter = $counter;
        $this->keyHandleBinary = $keyHandleBinary;
        $this->publicKeyBinary = $publicKeyBinary;
    }

    public function getAttestationCertificateBinary(): string
    {
        return $this->attestationCertificateBinary;
    }

    public function getCounter(): int
    {
        return $this->counter;
    }

    public function getKeyHandleBinary(): string
    {
        return $this->keyHandleBinary;
    }

    public function getpublicKeyBinary(): string
    {
        return $this->publicKeyBinary;
    }

    public function serialize()
    {
        return serialize([
            $this->attestationCertificateBinary,
            $this->counter,
            $this->keyHandleBinary,
            $this->publicKeyBinary,
        ]);
    }

    public function unserialize($serialized)
    {
        list(
            $this->attestationCertificateBinary,
            $this->counter,
            $this->keyHandleBinary,
            $this->publicKeyBinary) = unserialize($serialized);
    }
}
