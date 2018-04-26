<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Model;

use Serializable;

/**
 * Interface for representing a websafe-base64 encoded U2F registration.
 *
 * @todo Add getOwnerId()?
 */
interface IU2fRegistration extends Serializable
{
    /**
     * @return string The websafe-base64 encoded attestation of the U2F token.
     */
    public function getAttestationCertificate(): string;

    /**
     * @return int The counter for this U2F registration.
     */
    public function getCounter(): int;

    /**
     * @return string The websafe-base64 encoded key handle of the U2F
     * registration.
     */
    public function getKeyHandle(): string;

    /**
     * @return string The websafe-base64 encoded public key of the U2F
     * registration.
     */
    public function getPublicKey(): string;
}
