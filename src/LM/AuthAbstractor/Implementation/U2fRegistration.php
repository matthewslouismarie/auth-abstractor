<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Implementation;

use LM\AuthAbstractor\Model\IU2fRegistration;

/**
 * Implementation of IU2fRegistration. Its strings are all encoded.
 */
class U2fRegistration implements IU2fRegistration
{
    /** @var string */
    private $attestationCertificate;

    /** @var int */
    private $counter;

    /** @var string */
    private $keyHandle;

    /** @var string */
    private $publicKey;

    /**
     * @param string $attestationCertificate The websafe-base64 encoding of the
     * attestation certificate of the U2F token.
     * @param int $counter The counter of the U2F token.
     * @param string $keyHandle The websafe-base64 encoding of the U2F
     * registration key handle.
     * @param string $publicKey The websafe-base64 encoding of the U2F
     * registration public key.
     */
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
