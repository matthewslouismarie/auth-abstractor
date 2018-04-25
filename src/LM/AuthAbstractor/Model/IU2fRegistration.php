<?php

declare(strict_types=1);

namespace LM\AuthAbstractor\Model;

use Serializable;

/**
 * @todo Add getOwnerId()?
 */
interface IU2fRegistration extends Serializable
{
    public function getAttestationCertificate(): string;

    public function getCounter(): int;

    public function getKeyHandle(): string;

    public function getPublicKey(): string;
}
