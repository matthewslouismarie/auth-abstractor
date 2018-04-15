<?php

namespace LM\Authentifier\Model;

use Serializable;

/**
 * @todo Add getOwnerId()?
 */
interface IU2fRegistration extends Serializable
{
    public function getAttestationCertificateBinary(): string;

    public function getCounter(): int;

    public function getKeyHandleBinary(): string;

    public function getPublicKeyBinary(): string;
}
