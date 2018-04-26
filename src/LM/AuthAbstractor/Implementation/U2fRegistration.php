<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Implementation;

use LM\AuthAbstractor\Model\IU2fRegistration;

/**
 * Implementation of IU2fRegistration. Its strings are all encoded.
 */
class U2fRegistration implements IU2fRegistration
{
    private $attestationCertificate;

    private $counter;

    private $keyHandle;

    private $publicKey;

    public function __construct(
        string $attestationCertificate,
        int $counter,
        string $keyHandle,
        string $publicKey
    ) {
        $this->attestationCertificate = $attestationCertificate;
        $this->counter = $counter;
        $this->keyHandle = $keyHandle;
        $this->publicKey = $publicKey;
    }

    public function getAttestationCertificate(): string
    {
        return $this->attestationCertificate;
    }

    public function getCounter(): int
    {
        return $this->counter;
    }

    public function getKeyHandle(): string
    {
        return $this->keyHandle;
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    public function serialize()
    {
        return serialize([
            $this->attestationCertificate,
            $this->counter,
            $this->keyHandle,
            $this->publicKey,
        ]);
    }

    public function unserialize($serialized)
    {
        list(
            $this->attestationCertificate,
            $this->counter,
            $this->keyHandle,
            $this->publicKey) = unserialize($serialized);
    }
}
