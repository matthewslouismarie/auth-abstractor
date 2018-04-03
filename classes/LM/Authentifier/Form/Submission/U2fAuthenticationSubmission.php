<?php

namespace LM\Authentifier\Form\Submission;

use Serializable;

class U2fAuthenticationSubmission implements Serializable
{
    private $u2fTokenResponse;

    public function __construct(?string $u2fTokenResponse = null)
    {
        $this->u2fTokenResponse = $u2fTokenResponse;
    }

    public function getU2fTokenResponse(): ?string
    {
        return $this->u2fTokenResponse;
    }

    public function setU2fTokenResponse(?string $u2fTokenResponse): void
    {
        $this->u2fTokenResponse = $u2fTokenResponse;
    }

    public function serialize(): string
    {
        return $this->u2fTokenResponse;
    }

    public function unserialize($serialized): void
    {
        $this->u2fTokenResponse = $serialized;
    }
}
