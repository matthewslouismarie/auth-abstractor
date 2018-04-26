<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Model;

use Serializable;

/**
 * Interface for representing a U2F registration.
 *
 * @todo Add getOwnerId()?
 */
interface IU2fRegistration extends Serializable
{
    public function getAttestationCertificate(): string;

    public function getCounter(): int;

    public function getKeyHandle(): string;

    public function getPublicKey(): string;
}
